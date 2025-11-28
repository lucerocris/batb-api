<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Services\FileUploadService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Hash password if provided
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        // Remove password_confirmation and image from data
        unset($data['password_confirmation']);
        unset($data['image']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $user = $this->record;

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
                $storedPath = $fileUploadService->handleUserImage($user, $file);
                
                if ($storedPath) {
                    $user->update(['image_path' => $storedPath]);
                    // Clean up temporary Filament upload
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($imagePath);
                }
            }
        }
    }
}
