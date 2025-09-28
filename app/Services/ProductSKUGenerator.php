<?php

namespace App\Services;
use App\Models\Category;
use App\Models\Product;
/**
 * GENERATES UNIQUE SKU BY CATEGORY
 * EXAMPLE NECKLACE NECK-01002
 */
class ProductSKUGenerator{
    public static function generateSKU($categoryId){
        $category = Category::find($categoryId);
        if(!$category){
            return null;
        }
        $prefix = strtoupper(substr($category->name, 0, 4));
        do{
            $randomNumber = str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);
            $productSKU = $prefix . '-' . $randomNumber;
        }while (Product::where('sku', $productSKU)->exists());

        return $productSKU;
    } 
}