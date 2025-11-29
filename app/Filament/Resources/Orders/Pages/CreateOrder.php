<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Services\FileUploadService;
use App\Services\OrderNumberGenerator;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Generate order number if not provided
        if (empty($data['order_number'])) {
            $data['order_number'] = OrderNumberGenerator::generateRandomOrderNumber();
        }

        // Set default values
        if (empty($data['status'])) {
            $data['status'] = 'pending';
        }
        if (empty($data['payment_status'])) {
            $data['payment_status'] = 'pending';
        }
        if (empty($data['currency'])) {
            $data['currency'] = 'USD';
        }
        if (empty($data['order_date'])) {
            $data['order_date'] = now();
        }

        // Remove payment_proof from data as we'll handle it separately
        unset($data['payment_proof']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $order = $this->record;

        // Handle payment proof upload using service if uploaded
        if (isset($this->data['payment_proof']) && is_array($this->data['payment_proof']) && !empty($this->data['payment_proof'][0])) {
            $imagePath = $this->data['payment_proof'][0];
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
                $storedPath = $fileUploadService->handleOrderPaymentImage($order, $file);
                
                if ($storedPath) {
                    $order->update(['image_path' => $storedPath]);
                    // Clean up temporary Filament upload
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($imagePath);
                }
            }
        }
    }
}
