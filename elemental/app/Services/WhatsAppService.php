<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class WhatsAppService
{
    public function sendTemplateMessage($orderId, $statusId)
    {
        // Obtener la orden
        $order = DB::table('orders')->where('id', $orderId)->first();

        if (!$order) {
            return;
        }

        // Obtener el cliente asociado a la orden
        $customer = DB::table('customers')->where('id', $order->customer_id)->first();

        if (!$customer) {
            return;
        }

        // Verificar si el estado es uno de los que requiere enviar un mensaje
        $campaign = DB::table('campaigns')->where('status_id', $statusId)->first();

        if (!$campaign) {
            return;
        }

        // Obtener el nombre del template desde campaign_templates
        $template = DB::table('campaign_templates')->where('campaign_id', $campaign->id)->value('template_name');

        if (!$template) {
            return;
        }

        // Configurar los datos para la solicitud cURL
        $url = 'https://api.watoolbox.com/webhooks/W1WQJ0JLS';
        $data = [
            'action' => 'send-template',
            'template' => $template,
            'phone' => $customer->phone, // Obtener el teléfono del cliente
        ];

        // Enviar la solicitud cURL
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        ]);

        try {
            $response = curl_exec($ch);
            if ($response === false) {
                throw new \Exception(curl_error($ch), curl_errno($ch));
            }
        } catch (\Exception $e) {
            // Manejar el error
            error_log($e->getMessage());
        } finally {
            curl_close($ch);
        }
    }
}