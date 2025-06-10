<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WooCommerceService;

class WooTestCommand extends Command
{
    protected $signature = 'woo:test';
    protected $description = 'Prueba conexión con WooCommerce';

    public function handle(WooCommerceService $woo)
    {
        $this->info('Conectando a WooCommerce...');
        try {
            $products = $woo->listProducts(['per_page' => 5]);
            foreach ($products as $p) {
                $this->line("✔ {$p->id}: {$p->name} - {$p->price} COP");
            }

        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }
}
