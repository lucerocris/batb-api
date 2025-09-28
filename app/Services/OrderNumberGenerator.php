<?php

namespace App\Services;

use App\Models\Order;

class OrderNumberGenerator{

    public static function generateRandomOrderNumber(){
        do{
            $randomNumber = str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);
            $orderNumber = 'ORD-' . $randomNumber;
        }while (Order::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }
}