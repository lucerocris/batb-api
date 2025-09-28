<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductCreationService
{
    public function __construct(
        private FileUploadService $fileUploadService,
        private InventoryMovementService $inventoryService,
        private ProductVariantService $variantService,
        private OriginalProductService $originalService
    ) {}

    /**
     * Create a complete product with all related data
     */
    public function createProduct(array $productData, array $variantsData = []): Product
    {
        return DB::transaction(function () use ($productData, $variantsData) {
            $product = Product::create($productData);
            
            $this->logProductInventory($product);
            $this->handleProductImage($product, $productData['image'] ?? null);
            
            if (!empty($variantsData)) {
                $this->originalService->makeOriginalVariant($product);
                $this->variantService->createVariantsForNewProduct($product, $variantsData);
            }
            
            return $product;
        });
    }

    /**
     * Handle product image upload
     */
    private function handleProductImage(Product $product, $image): void
    {
        if (!$image instanceof \Illuminate\Http\UploadedFile) {
            return;
        }

        $storedPath = $this->fileUploadService->handleProductImage($product, $image);
        if ($storedPath) {
            $product->image_path = $storedPath;
            $product->save();
        }
    }

    /**
     * Log initial product inventory
     */
    private function logProductInventory(Product $product): void
    {
        $this->inventoryService->newProductLog([
            'product_id' => $product->id,
            'quantity' => $product->stock_quantity ?? 0,
        ]);
    }
}
