<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['slug']) && ! empty($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Handle image gallery uploads
        // Filament's FileUpload component automatically stores files and returns paths
        if (isset($data['image_gallery']) && is_array($data['image_gallery']) && !empty($data['image_gallery'])) {
            // Filter out any non-string values (shouldn't happen, but safety check)
            $imagePaths = array_filter($data['image_gallery'], fn($image) => is_string($image) && !empty($image));
            $imagePaths = array_values($imagePaths); // Re-index array
            
            // Set first image as thumbnail
            if (!empty($imagePaths)) {
                $data['image_path'] = $imagePaths[0];
                $data['image_gallery'] = $imagePaths;
            } else {
                $data['image_gallery'] = null;
                $data['image_path'] = null;
            }
        } else {
            $data['image_gallery'] = null;
            $data['image_path'] = null;
        }

        return $data;
    }
}
