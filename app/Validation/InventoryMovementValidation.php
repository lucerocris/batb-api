<?php

namespace App\Validation;

use App\Models\Product;
use App\Models\ProductVariant;

class InventoryMovementValidation{
    public static function prepare(array $data)
    {
        if (!isset($data['initial_quantity'])) {
            if (!empty($data['product_variant_id'])) {
                $data['initial_quantity'] = ProductVariant::where('id', $data['product_variant_id'])
                    ->value('stock_quantity') ?? 0;
            } else {
                $data['initial_quantity'] = Product::where('id', $data['product_id'])
                    ->value('stock_quantity') ?? 0;
            }
        }

        return $data;
    }

    public static function rules(){
        return [
            'product_id' => 'required|uuid|exists:products,id',
            'product_variant_id' => 'nullable|integer|exists:product_variants,id',
            'order_id' => 'nullable|uuid|exists:orders,id',
            'user_id' => 'nullable|uuid|exists:users,id',
            'type' => 'required|in:restock,lost,damaged,correction,other,creation',
            "adjustment_type" => 'required|string|in:increase,decrease',
            'quantity' => 'required|integer',
            'initial_quantity' => 'required|integer',
            'notes' => 'nullable|string',
            'reference' => 'nullable|string|max:255',
            'meta_data' => 'nullable|array',
        ];
    }
}
