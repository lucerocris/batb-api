<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
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
        $directory = $this->resolveProductDirectory($product);
        $filename = $this->generateProductFilename($product, $file);
        
        return $this->uploadFileWithFixedName($file, $directory, $filename);
    }

    /**
     * Handle gallery uploads for a Product entity.
     *
     * @param  array<UploadedFile>  $files
     * @return array<int, string>
     */
    public function handleProductGalleryImages(Product $product, array $files): array
    {
        $storedPaths = [];

        foreach ($files as $index => $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $stored = $this->handleProductGalleryImage($product, $file, $index + 1);

            if ($stored) {
                $storedPaths[] = $stored;
            }
        }

        return $storedPaths;
    }

    /**
     * Store a single gallery image for the product.
     */
    public function handleProductGalleryImage(Product $product, UploadedFile $file, int $sequence = 1): ?string
    {
        $directory = $this->resolveProductDirectory($product) . '/gallery';
        $filename = $this->generateProductGalleryFilename($product, $file, $sequence);

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

    private function generateProductGalleryFilename(Product $product, UploadedFile $file, int $sequence): string
    {
        $ext = $file->getClientOriginalExtension() ?: 'jpg';

        return $product->slug . '-gallery-' . $sequence . '-' . Str::random(6) . '.' . strtolower($ext);
    }

    private function resolveProductDirectory(Product $product): string
    {
        $categoryName = $product->category?->name;

        if (! $categoryName) {
            return 'products/' . $product->slug;
        }

        return 'products/' . Str::slug($categoryName) . '/' . $product->slug;
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
