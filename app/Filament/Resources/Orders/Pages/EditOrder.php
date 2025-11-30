<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Services\FileUploadService;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Handle payment proof - remove from data as we'll handle separately
        unset($data['payment_proof']);

        return $data;
    }

    protected function afterSave(): void
    {
        $order = $this->record;

        // Handle payment proof upload using service if new proof was uploaded
        if (isset($this->data['payment_proof']) && is_array($this->data['payment_proof']) && !empty($this->data['payment_proof'][0])) {
            $imagePath = $this->data['payment_proof'][0];
            $fullPath = storage_path('app/public/' . $imagePath);
            
            // Check if it's a new upload (not the existing image path)
            if (file_exists($fullPath) && $imagePath !== $order->image_path) {
                $file = new \Illuminate\Http\UploadedFile(
                    $fullPath,
                    basename($imagePath),
                    mime_content_type($fullPath),
                    null,
                    true
                );
                
                $fileUploadService = app(FileUploadService::class);
                
                // Use replaceImage to handle cleanup and upload
                $storedPath = $fileUploadService->replaceImage(
                    $order->image_path,
                    fn() => $fileUploadService->handleOrderPaymentImage($order, $file)
                );
                
                if ($storedPath) {
                    $order->update(['image_path' => $storedPath]);
                    // Clean up temporary Filament upload
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($imagePath);
                }
            }
        }
    }
}
