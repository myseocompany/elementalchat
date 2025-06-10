<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product as Product;
use App\Models\ProductSnapshot;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Services\WooCommerceService;


class SyncProductsFromMekano extends Command
{
    protected $signature = 'mekano:sync-products';
    protected $description = 'Sincroniza productos desde el ERP Mekano, guarda snapshot y actualiza inventario.';

    public function handle(WooCommerceService $woo)
    {
        if ($this->hasMutex()) {
            $this->info('Ya se está ejecutando otro proceso, se salta.');
            return 0;
        }

        $this->setMutex();

        try {
            $productos = $this->fetchProducts();

            if (empty($productos)) {
                Log::error('Sync Mekano falló: respuesta vacía.');
                $this->error('La respuesta de Mekano está vacía. No se realizará sincronización.');
                return 1;
            }

            $this->resetSnapshot();
            $this->saveSnapshot($productos);
            $this->syncInventory($productos);

            // 👉 Aquí haces la sincronización con WooCommerce
            $this->syncWooCommerce($productos, $woo);

            $this->info('Sincronización completada.');
            Log::info('Sync Mekano ejecutado correctamente a las ' . now());
        } catch (\Exception $e) {
            Log::error('Error durante la sincronización: ' . $e->getMessage());
            $this->error('Error durante la sincronización: ' . $e->getMessage());
            return 1;
        } finally {
            $this->clearMutex();
        }

        return 0;
}

    private function fetchProducts()
    {
        $this->info('Conectando a Mekano...');
        
        $str_url = "http://144.202.47.24/MB757_prueba/RestServerIsapi.dll/api/v1/TApoloRestInterface/executequery";
        $str_url_test = "https://n8n-1-85-1.onrender.com/webhook/cf2ffb57-8608-42d8-82cd-97d3ae8a3a6a";

        $payload = [
            "CLAVE" => "Get_Inventario_Precio",
            "FECHA_CORTE_" => Carbon::now()->format('d.m.Y'), // fecha actual
            "REFERENCIA_" => ""
        ];

        $response = Http::timeout(30)->post($str_url, $payload);

        if (!$response->successful()) {
            throw new \Exception('Error conectando con Mekano. Código: ' . $response->status());
        }

        return $response->json('data');
    }


    private function resetSnapshot()
    {
        $this->info('Limpiando snapshot anterior...');
        ProductSnapshot::truncate();
    }

    private function saveSnapshot(array $productos)
    {
        $this->info('Guardando nuevo snapshot...');
        foreach ($productos as $producto) {
            ProductSnapshot::create([
                'reference' => $producto['REFERENCIA'],
                'name' => $producto['NOMBRE_REFERENCIA'],
                'price' => $producto['PRECIO'],
                'quantity' => $producto['SALDO'],
                'created_at' => Carbon::now(),
            ]);
        }
    }

    private function syncInventory(array $productos)
    {
        $this->info('Comparando snapshot con productos actuales...');

        // Desactivar todos los productos primero
        Product::query()->update(['status_id' => 2]); // 2 = Inactivo

        foreach ($productos as $producto) {
            $existing = Product::where('reference', $producto['REFERENCIA'])->first();

            if ($existing) {
                $existing->update([
                    'name' => $producto['NOMBRE_REFERENCIA'],
                    'price' => $producto['PRECIO'],
                    'quantity' => $producto['SALDO'],
                    'status_id' => $producto['SALDO'] > 0 ? 1 : 2,
                ]);
            } else {
                Product::create([
                    'name' => $producto['NOMBRE_REFERENCIA'],
                    'reference' => $producto['REFERENCIA'],
                    'price' => $producto['PRECIO'],
                    'quantity' => $producto['SALDO'],
                    'category_id' => 1, // Categoría default
                    'status_id' => $producto['SALDO'] > 0 ? 1 : 2,
                ]);
            }
        }
      

    }

    private function hasMutex()
    {
        return cache()->has('sync-products-running');
    }

    private function setMutex()
    {
        cache()->put('sync-products-running', true, 600); // 10 minutos
    }

    private function clearMutex()
    {
        cache()->forget('sync-products-running');
    }
private function syncWooCommerce(array $productos, $wooService)
{
    $this->info('Sincronizando productos con WooCommerce...');

    // Paso 1: Descargar todos los productos existentes
    $this->info('Descargando productos desde WooCommerce...');
    $wooProducts = [];
    $page = 1;

    do {
        $response = $wooService->getClient()->get('products', [
            'per_page' => 100,
            'page' => $page,
            'status' => 'any' // incluye draft y publish
        ]);

        foreach ($response as $item) {
            if ($item->sku) {
                $wooProducts[$item->sku] = $item;
            }
        }

        $hasMore = count($response) === 100;
        $page++;
    } while ($hasMore);

    $mekanoSkus = [];

    // Paso 2: Recorrer productos de Mekano
    foreach ($productos as $producto) {
        $ref = $producto['REFERENCIA'];
        $name = $producto['NOMBRE_REFERENCIA'];
        $price = (string) $producto['PRECIO'];
        $quantity = (int) $producto['SALDO'];

        if (stripos($name, 'magis') !== false) {
            $this->line("⏭ Magistral omitido: $ref ($name)");
            continue;
        }

        $mekanoSkus[] = $ref;

        if (isset($wooProducts[$ref])) {
            $wooProduct = $wooProducts[$ref];
            $currentPrice = (string) $wooProduct->regular_price;
            $currentStock = (int) $wooProduct->stock_quantity;
            $currentStatus = $wooProduct->status;
            $newStatus = $quantity > 0 ? 'publish' : 'draft';

            if (
                $price !== $currentPrice ||
                $quantity !== $currentStock ||
                $newStatus !== $currentStatus
            ) {
                $wooService->updateProduct($wooProduct->id, [
                    'name' => $name,
                    'regular_price' => $price,
                    'stock_quantity' => $quantity,
                    'manage_stock' => true,
                    'status' => $newStatus,
                ]);
                $this->line("🔄 Actualizado Woo: $ref");
            } else {
                $this->line("✅ Sin cambios: $ref");
            }
        } else {
            if ($quantity > 0) {
                $wooService->createProduct([
                    'name' => $name,
                    'type' => 'simple',
                    'regular_price' => $price,
                    'sku' => $ref,
                    'stock_quantity' => $quantity,
                    'manage_stock' => true,
                    'status' => 'publish',
                ]);
                $this->line("🆕 Creado en Woo: $ref");
            } else {
                $this->line("⏭ Sin stock y no existe en Woo: $ref (no se crea)");
            }
        }
    }

    // Paso 3: Despublicar productos que ya no están en Mekano
    $this->info('Despublicando productos huérfanos...');
    foreach ($wooProducts as $sku => $wooProduct) {
        if (!in_array($sku, $mekanoSkus)) {
            if ($wooProduct->status !== 'draft') {
                try {
                    $wooService->updateProduct($wooProduct->id, [
                        'status' => 'draft',
                    ]);
                    $this->line("🗑 Despublicado huérfano: $sku");
                } catch (\Exception $e) {
                    $this->error("❌ Error al despublicar $sku: " . $e->getMessage());
                }
            }
        }
    }
}

}
