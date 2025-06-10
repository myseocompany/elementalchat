<?php

namespace App\Services;

use Automattic\WooCommerce\Client;

class WooCommerceService
{
    protected $woocommerce;

    public function __construct()
    {
        $this->woocommerce = new Client(
            config('woocommerce.url'),
            config('woocommerce.consumer_key'),
            config('woocommerce.consumer_secret'),
            [
                'version' => 'wc/v3',
                'verify_ssl' => false, // cambia a true en producción si hay SSL válido
            ]
        );
    }

    public function getClient()
    {
        return $this->woocommerce;
    }

    public function listProducts($params = [])
    {
        return $this->woocommerce->get('products', $params);
    }

    public function updateProduct($id, $data)
    {
        return $this->woocommerce->put("products/{$id}", $data);
    }

    public function createProduct($data)
    {
        return $this->woocommerce->post("products", $data);
    }
}
