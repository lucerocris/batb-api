<?php

namespace App\Services;

use App\Models\ProductVariant;
use App\Models\Product;
use App\Services\ProductVariantSKUGenerator;

class OriginalProductService{

    protected ProductVariantSKUGenerator $generator;

    // Constructor injection — Laravel will auto-resolve this
    public function __construct(ProductVariantSKUGenerator $generator)
    {
        $this->generator = $generator;
    }

    public function makeOriginalVariant(Product $product){
        if ($product->productVariants()->exists()) {
            return null;
        }

        // Create initial variant
        return ProductVariant::create([
            'product_id'     => $product->id,
            'name'           => "{$product->name} - Original",
            'sku'            => $this->generator->generateSKU($product->id),
            'stock_quantity' => $product->stock_quantity,
            'image_path' => $product->image_path,
            'attributes'     => ['initial' => true],
        ]);
    }
}
