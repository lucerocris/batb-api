<?php

namespace App\Services;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Illuminate\Support\Log;


class OrderItemAutoFillService{
    private ?Collection $products = null;
    private ?Collection $variants = null;

    /**
     * Preload product and variant data for multiple order items
     */
    public function preloadData(array $orderItems): void
    {
        $productIds = collect($orderItems)
            ->pluck('product_id')
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        $variantIds = collect($orderItems)
            ->pluck('product_variant_id')
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        $this->products = Product::whereIn('id', $productIds)->get()->keyBy('id');
        $this->variants = ProductVariant::whereIn('id', $variantIds)->get()->keyBy('id');
    }

    /**
     * Fills product details for the orderItem using preloaded data
     */
    public function fillProductDataBatch(array $orderItems): array
    {
        // Collect all unique IDs
        $productIds = collect($orderItems)
            ->pluck('product_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $variantIds = collect($orderItems)
            ->pluck('product_variant_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        // Batch load all products and variants in 2 queries
        $products = Product::whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        $variants = ProductVariant::whereIn('id', $variantIds)
            ->get()
            ->keyBy('id');

        // Process each item using the pre-loaded data
        return array_map(function($item) use ($products, $variants) {
            return $this->fillProductData($item, $products, $variants);
        }, $orderItems);
    }

    /**
     * Fills product details for a single orderItem (uses pre-loaded collections)
     */
    public function fillProductData($item, ?Collection $products = null, ?Collection $variants = null)
    {
        $unitPrice = 0;

        // If collections not provided, load individually (backward compatibility)
        if ($products === null || $variants === null) {
            return $this->fillProductDataLegacy($item);
        }

        // Get product data and base price
        if (isset($item['product_id']) && $products->has($item['product_id'])) {
            $product = $products[$item['product_id']];
            $item['product_name'] = $product->name;
            $item['product_sku'] = $product->sku;

            // Use sale_price if available, otherwise base_price
            $unitPrice = $product->sale_price ?? $product->base_price;
        }

        // Add variant data and adjust price
        if (isset($item['product_variant_id']) && $variants->has($item['product_variant_id'])) {
            $variant = $variants[$item['product_variant_id']];
            $item['variant_name'] = $variant->name;
            $item['variant_sku'] = $variant->sku;

            // Apply variant price adjustment
            if ($variant->price_adjustment) {
                $unitPrice += $variant->price_adjustment;
            }
        }

        $item['unit_price'] = $unitPrice;

        return $item;
    }

    /**
     * Legacy method for backward compatibility (causes N+1)
     * @deprecated Use fillProductDataBatch instead
     */
    private function fillProductDataLegacy($item)
    {
        $unitPrice = 0;

        // Get product data and base price from preloaded data
        if (isset($item['product_id']) && $this->products) {
            $product = $this->products->get($item['product_id']);
            if ($product) {
                $item['product_name'] = $product->name;
                $item['product_sku'] = $product->sku;

                // Use sale_price if available, otherwise base_price
                $unitPrice = $product->sale_price ?? $product->base_price;
            }
        }

        // Add variant data and adjust price from preloaded data
        if (isset($item['product_variant_id']) && $this->variants) {
            $variant = $this->variants->get($item['product_variant_id']);
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

    /**
     * Reset preloaded data (useful for batch processing)
     */
    public function reset(): void
    {
        $this->products = null;
        $this->variants = null;
    }

}
