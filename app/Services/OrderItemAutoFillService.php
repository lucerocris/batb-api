<?php

namespace App\Services;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Log;


class OrderItemAutoFillService{
    /**
     * Fills product details for the orderItem
     */
    public function fillProductData($item)
    {
        $unitPrice = 0;
        // Get product data and base price
        if (isset($item['product_id'])) {
            $product = Product::find($item['product_id']);
            if ($product) {
                $item['product_name'] = $product->name;
                $item['product_sku'] = $product->sku;
                
                // Use sale_price if available, otherwise base_price
                $unitPrice = $product->sale_price ?? $product->base_price;
            }
        }
        
        // Add variant data and adjust price
        if (isset($item['product_variant_id'])) {
            $variant = ProductVariant::find($item['product_variant_id']);
            if ($variant) {
                $item['variant_name'] = $variant->name;
                $item['variant_sku'] = $variant->sku;
                
                // Apply variant price adjustment
                if ($variant->price_adjustment) {
                    $unitPrice += $variant->price_adjustment;
                }
            }
        }

        $item['unit_price'] = $unitPrice;

        return $item;
    }

}