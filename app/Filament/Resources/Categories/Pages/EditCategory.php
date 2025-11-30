<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use App\Services\FileUploadService;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Generate slug if name changed and slug not manually set
        if (isset($data['name']) && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Handle image path - if it's the existing path, keep it, otherwise we'll process new upload
        if (isset($data['image']) && is_array($data['image'])) {
            $imagePath = $data['image'][0];
            // If it's already in categories directory structure, it's the existing image
            if (str_starts_with($imagePath, 'categories/') && str_starts_with($imagePath, $this->record->image_path ?? '')) {
                // Keep existing path
                $data['image_path'] = $this->record->image_path;
            }
            // Remove from data as we'll handle separately
            unset($data['image']);
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $category = $this->record;
        $oldName = $category->getOriginal('name');
        $newName = $category->name;

        // Handle image upload/replacement using service
        if (isset($this->data['image']) && is_array($this->data['image']) && !empty($this->data['image'][0])) {
            $imagePath = $this->data['image'][0];
            $fullPath = storage_path('app/public/' . $imagePath);
            
            // Check if it's a new upload (not the existing image path)
            if (file_exists($fullPath) && $imagePath !== $category->image_path) {
                $file = new \Illuminate\Http\UploadedFile(
                    $fullPath,
                    basename($imagePath),
                    mime_content_type($fullPath),
                    null,
                    true
                );
                
                $fileUploadService = app(FileUploadService::class);
                
                // Handle name change image move
                if ($oldName !== $newName && $category->image_path) {
                    $movedPath = $fileUploadService->moveCategoryImage($category, $oldName, $newName);
                    if ($movedPath) {
                        $category->update(['image_path' => $movedPath]);
                    }
                }
                
                // Handle new image upload
                $storedPath = $fileUploadService->handleCategoryImage($category, $file, true);
                if ($storedPath) {
                    $category->update(['image_path' => $storedPath]);
                    // Clean up temporary Filament upload
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($imagePath);
                }
            }
        } elseif ($oldName !== $newName && $category->image_path) {
            // Just move the image if name changed but no new image uploaded
            $fileUploadService = app(FileUploadService::class);
            $movedPath = $fileUploadService->moveCategoryImage($category, $oldName, $newName);
            if ($movedPath) {
                $category->update(['image_path' => $movedPath]);
            }
        }
    }
}
