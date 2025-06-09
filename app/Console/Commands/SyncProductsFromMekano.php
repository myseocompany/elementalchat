<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MekanoProduct as Product;
use App\Models\ProductSnapshot;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SyncProductsFromMekano extends Command
{
    protected $signature = 'mekano:sync-products';
    protected $description = 'Sincroniza productos desde el ERP Mekano, guarda snapshot y actualiza inventario.';

    public function handle()
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
}
