<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Order;
use App\Traits\HandlesFileUpload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class FileUploadService
{
    use HandlesFileUpload;

    /**
     * Handle file upload for a Product entity
     */
    public function handleProductImage(Product $product, UploadedFile $file): ?string
    {
        $categoryName = $product->category?->name;
        if (!$categoryName) {
            return null;
        }

        $directory = 'products/' . Str::slug($categoryName) . '/' . $product->slug;
        $filename = $this->generateProductFilename($product, $file);
        
        return $this->uploadFileWithFixedName($file, $directory, $filename);
    }

    /**
     * Handle file upload for a Category entity
     */
    public function handleCategoryImage(Category $category, UploadedFile $file, bool $cleanDirectory = true): ?string
    {
        $directory = 'categories/' . Str::slug($category->name);
        return $this->uploadFileToDirectory($file, $directory, $cleanDirectory);
    }

    /**
     * Handle file upload for a User entity
     */
    public function handleUserImage(User $user, UploadedFile $file): ?string
    {
        return $this->uploadFile($file, 'users');
    }

    /**
     * Handle file upload for a ProductVariant entity
     */
    public function handleProductVariantImage(ProductVariant $variant, UploadedFile $file): ?string
    {
        $product = $variant->product()->with('category')->first();
        $categoryName = $product?->category?->name;
        
        if (!$categoryName) {
            return null;
        }

        $directory = 'products/' . Str::slug($categoryName) . '/' . $product->slug;
        $filename = $this->generateProductVariantFilename($variant, $product, $file);
        
        return $this->uploadFileWithFixedName($file, $directory, $filename);
    }

    /**
     * Handle file upload for an Order entity (payment proof)
     */
    public function handleOrderPaymentImage(Order $order, UploadedFile $file): ?string
    {
        $directory = 'orders/payments';
        $filename = $this->generateOrderFilename($order, $file);
        
        return $this->uploadFileWithFixedName($file, $directory, $filename);
    }

    /**
     * Handle file replacement with cleanup for any entity
     */
    public function replaceImage(?string $oldPath, callable $uploadCallback): ?string
    {
        if ($oldPath) {
            $this->deleteFile($oldPath);
        }
        
        return $uploadCallback();
    }

    /**
     * Handle category image move when category name changes
     */
    public function moveCategoryImage(Category $category, string $oldName, string $newName): ?string
    {
        if (!$category->image_path) {
            return null;
        }

        $oldDirectory = 'categories/' . Str::slug($oldName);
        $newDirectory = 'categories/' . Str::slug($newName);
        
        return $this->moveFileToDirectory($category->image_path, $newDirectory);
    }

    /**
     * Generate filename for Product images
     */
    private function generateProductFilename(Product $product, UploadedFile $file): string
    {
        $ext = $file->getClientOriginalExtension();
        return $product->slug . '.' . strtolower($ext ?: 'jpg');
    }

    /**
     * Generate filename for ProductVariant images
     */
    private function generateProductVariantFilename(ProductVariant $variant, Product $product, UploadedFile $file): string
    {
        $ext = $file->getClientOriginalExtension();
        $size = data_get($variant->attributes, 'size');
        $namePart = $product->slug . ($size ? ('-size-' . Str::slug((string) $size)) : '');
        
        return $namePart . '.' . strtolower($ext ?: 'jpg');
    }

    /**
     * Generate filename for Order payment images
     */
    private function generateOrderFilename(Order $order, UploadedFile $file): string
    {
        $ext = $file->getClientOriginalExtension();
        return $order->order_number . '.' . strtolower($ext ?: 'jpg');
    }
}
