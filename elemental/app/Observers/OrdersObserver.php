<?php
// app/Observers/OrderObserver.php

namespace App\Observers;

use App\Order;
use App\OrderHistory;

class OrderObserver
{
    public function saving(Order $order)
    {
        \Log::info('Order saving event triggered');  

        if ($order->isDirty()) {  // Comprueba si algún atributo ha cambiado
            $orderHistory = $order->getOriginal();  // Obtiene los atributos originales del pedido
            $orderHistory['order_id'] = $orderHistory['id'];  // Establece el order_id al id original
            unset($orderHistory['id']);  // Elimina el atributo id
            OrderHistory::create($orderHistory);  // Crea un nuevo registro de historial de pedidos
        }

    }
}
