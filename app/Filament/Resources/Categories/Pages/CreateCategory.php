<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use App\Services\FileUploadService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Generate slug if not provided
        if (empty($data['slug']) && !empty($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Remove image from data as we'll handle it separately
        unset($data['image']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $category = $this->record;

        // Handle image upload using service if image was uploaded
        if (isset($this->data['image']) && is_array($this->data['image']) && !empty($this->data['image'][0])) {
            $imagePath = $this->data['image'][0];
            $fullPath = storage_path('app/public/' . $imagePath);
            
            if (file_exists($fullPath)) {
                $file = new \Illuminate\Http\UploadedFile(
                    $fullPath,
                    basename($imagePath),
                    mime_content_type($fullPath),
                    null,
                    true
                );
                
                $fileUploadService = app(FileUploadService::class);
                $storedPath = $fileUploadService->handleCategoryImage($category, $file, true);
                
                if ($storedPath) {
                    $category->update(['image_path' => $storedPath]);
                    // Clean up temporary Filament upload
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($imagePath);
                }
            }
        }
    }
}
