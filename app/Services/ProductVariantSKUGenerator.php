<?php

namespace App\Services;
use App\Models\Product;
use App\Models\ProductVariant;

class ProductVariantSKUGenerator
{
    public static function generateSKU($productId)
    {
        $product = Product::find($productId);
        if (!$product) {
            return null;
        }
        $prefix = $product->sku . '-';
        do {
            $randomNumber = str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);
            $variantSKU = $prefix . $randomNumber;
        } while (ProductVariant::where('sku', $variantSKU)->exists());

        return $variantSKU;
    }
}