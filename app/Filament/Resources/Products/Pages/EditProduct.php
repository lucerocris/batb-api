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
