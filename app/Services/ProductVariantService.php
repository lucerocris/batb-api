<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;

class ProductVariantService
{
    public function __construct(
        private FileUploadService $fileUploadService,
        private InventoryMovementService $inventoryService,
        private OriginalProductService $originalService
    ) {}

    /**
     * Handle variants for product update
     */
    public function handleVariantsForUpdate(Product $product, array $variantsData): void
    {
        if (empty($variantsData)) {
            $product->productVariants()->delete();
            return;
        }

        $originalVariantId = $this->ensureOriginalVariant($product);
        
        $incomingIds = collect($variantsData)->pluck('id')->filter()->all();
        if ($originalVariantId) {
            $incomingIds[] = $originalVariantId;
        }

        // Delete variants not in the incoming request
        $product->productVariants()
            ->whereNotIn('id', $incomingIds)
            ->delete();

        // Update or create variants
        foreach ($variantsData as $variantData) {
            $this->processVariantData($product, $variantData);
        }
    }

    /**
     * Create variants for new product
     */
    public function createVariantsForNewProduct(Product $product, array $variantsData): void
    {
        foreach ($variantsData as $variantData) {
            $variant = $product->productVariants()->create($variantData);
            
            $this->handleVariantImage($variant, $variantData['image'] ?? null);
            $this->logVariantInventory($product, $variant);
        }
    }

    /**
     * Ensure original variant exists for product with variants
     */
    private function ensureOriginalVariant(Product $product): ?int
    {
        if ($product->productVariants()->count() === 0) {
            $originalVariant = $this->originalService->makeOriginalVariant($product);
            $product->load('productVariants');
            return $originalVariant?->id;
        }
        return null;
    }

    /**
     * Process individual variant data (update or create)
     */
    private function processVariantData(Product $product, array $variantData): void
    {
        if (!empty($variantData['id'])) {
            $this->updateExistingVariant($product, $variantData);
        } else {
            $this->createNewVariant($product, $variantData);
        }
    }

    /**
     * Update existing variant
     */
    private function updateExistingVariant(Product $product, array $variantData): void
    {
        $variant = $product->productVariants()->find($variantData['id']);
        if (!$variant) {
            return;
        }

        $updateData = collect($variantData)->except(['id', 'image'])->toArray();
        $variant->update($updateData);
        
        $this->handleVariantImage($variant, $variantData['image'] ?? null, true);
    }

    /**
     * Create new variant
     */
    private function createNewVariant(Product $product, array $variantData): void
    {
        $newVariant = $product->productVariants()->create($variantData);
        $this->handleVariantImage($newVariant, $variantData['image'] ?? null);
    }

    /**
     * Handle variant image upload/replacement
     */
    private function handleVariantImage(ProductVariant $variant, $image, bool $replace = false): void
    {
        if (!$image instanceof \Illuminate\Http\UploadedFile) {
            return;
        }

        if ($replace) {
            $storedPath = $this->fileUploadService->replaceImage(
                $variant->image_path,
                fn() => $this->fileUploadService->handleProductVariantImage($variant, $image)
            );
        } else {
            $storedPath = $this->fileUploadService->handleProductVariantImage($variant, $image);
        }

        if ($storedPath) {
            $variant->image_path = $storedPath;
            $variant->save();
        }
    }

    /**
     * Log inventory for new variant
     */
    private function logVariantInventory(Product $product, ProductVariant $variant): void
    {
        $this->inventoryService->newProductVariantLog([
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'quantity' => $variant->stock_quantity,
        ]);
    }
}
