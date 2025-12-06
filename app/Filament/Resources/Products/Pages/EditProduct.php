<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

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
        if (isset($data['name']) && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Handle image gallery uploads
        // Filament's FileUpload component automatically stores files and returns paths
        if (isset($data['image_gallery']) && is_array($data['image_gallery'])) {
            // Filter out any non-string values (shouldn't happen, but safety check)
            $imagePaths = array_filter($data['image_gallery'], fn($image) => is_string($image) && !empty($image));
            $imagePaths = array_values($imagePaths); // Re-index array
            
            // Set first image as thumbnail if there are images
            if (!empty($imagePaths)) {
                $data['image_path'] = $imagePaths[0];
                $data['image_gallery'] = $imagePaths;
            } else {
                // User removed all images
                $data['image_gallery'] = null;
                $data['image_path'] = null;
            }
        } else {
            // No image_gallery in data, keep existing or set to null
            $existingImages = $this->record->image_gallery ?? [];
            if (!empty($existingImages)) {
                $data['image_gallery'] = $existingImages;
                $data['image_path'] = $existingImages[0] ?? $data['image_path'] ?? null;
            } else {
                $data['image_gallery'] = null;
            }
        }

        return $data;
    }
}
