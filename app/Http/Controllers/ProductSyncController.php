<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductSnapshot;
use Illuminate\Http\Request;

class ProductSyncController extends Controller
{
    public function dashboard()
    {
        $productos = Product::all()->keyBy('reference');
        $snapshots = ProductSnapshot::all()->keyBy('reference');

        $diferencias = [];

        foreach ($snapshots as $ref => $snap) {
            if (isset($productos[$ref])) {
                $product = $productos[$ref];

                $cambios = [];

                if ($product->price != $snap->price) {
                    $cambios[] = 'Precio';
                }
                if ($product->quantity != $snap->quantity) {
                    $cambios[] = 'Cantidad';
                }
                if ($product->name != $snap->name) {
                    $cambios[] = 'Nombre';
                }

                if (count($cambios) > 0) {
                    $diferencias[] = [
                        'reference' => $ref,
                        'status' => 'CAMBIOS',
                        'cambios' => implode(', ', $cambios),
                        'actual' => $product,
                        'nuevo' => $snap,
                    ];
                } else {
                    $diferencias[] = [
                        'reference' => $ref,
                        'status' => 'IGUAL',
                        'cambios' => 'Sin cambios',
                        'actual' => $product,
                        'nuevo' => $snap,
                    ];
                }

                // Lo removemos para luego saber cuales sobran
                unset($productos[$ref]);
            } else {
                // Producto nuevo en snapshot, no existe en DB
                $diferencias[] = [
                    'reference' => $ref,
                    'status' => 'NUEVO',
                    'cambios' => 'Nuevo producto',
                    'actual' => null,
                    'nuevo' => $snap,
                ];
            }
        }

        // Los productos que quedaron en $productos no están en el snapshot => ELIMINADOS
        foreach ($productos as $ref => $product) {
            $diferencias[] = [
                'reference' => $ref,
                'status' => 'ELIMINADO',
                'cambios' => 'No existe en snapshot',
                'actual' => $product,
                'nuevo' => null,
            ];
        }

        return view('products.sync_dashboard', compact('diferencias'));
    }
}
