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

        $gallery = $this->normalizeGalleryInput($data['gallery_images'] ?? []);
        $data['gallery_images'] = $gallery;
        $data['image_path'] = $gallery[0] ?? null;

        return $data;
    }

    private function normalizeGalleryInput(null|string|array $value): array
    {
        return collect($value)
            ->when(! is_array($value), fn ($collection) => collect([$value]))
            ->filter(fn ($path) => is_string($path) && $path !== '')
            ->map(fn ($path) => ltrim($path, '/'))
            ->values()
            ->all();
    }
}
