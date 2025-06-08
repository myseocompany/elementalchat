<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\ProductSnapshot;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class SyncProductsFromMekano extends Command
{
    protected $signature = 'mekano:sync-products';
    protected $description = 'Sincroniza productos desde el ERP Mekano, guarda snapshot y actualiza inventario.';

    public function handle()
    {
        $this->info('Conectando a Mekano...');

        $response = Http::get('https://n8n-1-85-1.onrender.com/webhook/cf2ffb57-8608-42d8-82cd-97d3ae8a3a6a');

        if ($response->failed()) {
            $this->error('Error conectando con Mekano.');
            return 1;
        }

        $productos = $response->json('data');

        $this->info('Limpiando snapshot anterior...');
        ProductSnapshot::truncate(); // Elimina todos los datos previos

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

        $this->info('Comparando snapshot con productos actuales...');

        // Desactivar todos los productos primero (opcional)
        Product::query()->update(['status_id' => 2]); // 2 = Inactivo (ajusta según tu sistema)

        // Actualizar productos que siguen existiendo
        foreach ($productos as $producto) {
            $existing = Product::where('reference', $producto['REFERENCIA'])->first();

            if ($existing) {
                $existing->update([
                    'name' => $producto['NOMBRE_REFERENCIA'],
                    'price' => $producto['PRECIO'],
                    'quantity' => $producto['SALDO'],
                    'status_id' => $producto['SALDO'] > 0 ? 1 : 2, // Activo o Inactivo
                ]);
            } else {
                Product::create([
                    'name' => $producto['NOMBRE_REFERENCIA'],
                    'reference' => $producto['REFERENCIA'],
                    'price' => $producto['PRECIO'],
                    'quantity' => $producto['SALDO'],
                    'category_id' => 1, // Categoría default si aplica
                    'status_id' => $producto['SALDO'] > 0 ? 1 : 2,
                ]);
            }
        }

        $this->info('Sincronización completada.');
    }
}
