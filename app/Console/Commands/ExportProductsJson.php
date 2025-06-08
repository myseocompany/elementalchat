<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ExportProductsJson extends Command
{
    protected $signature = 'products:export-json';
    protected $description = 'Exportar productos como JSON con precios y cantidades aleatorias para pruebas.';

    public function handle()
    {
        $this->info('Exportando productos locales...');

        $productos = Product::select('reference', 'name')->get();

        $data = [];

        foreach ($productos as $producto) {
            // Datos aleatorios para simular inventario
            $precio = rand(5000, 500000); // Precio entre $5.000 y $500.000
            $cantidad = rand(0, 100); // Cantidad entre 0 y 100

            $data[] = [
                'REFERENCIA' => $producto->reference,
                'NOMBRE_REFERENCIA' => $producto->name,
                'PRECIO' => $precio,
                'SALDO' => $cantidad,
            ];
        }

        $output = [
            'data' => $data
        ];

        // Guardar JSON en Storage
        Storage::disk('local')->put('productos_fake.json', json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->info('Productos exportados en storage/app/productos_fake.json');
    }
}
