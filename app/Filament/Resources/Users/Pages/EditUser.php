<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Services\FileUploadService;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

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
        // Hash password if provided
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // Remove password_confirmation and image from data
        unset($data['password_confirmation']);
        unset($data['image']);

        return $data;
    }

    protected function afterSave(): void
    {
        $user = $this->record;

        // Handle image upload using service if new image was uploaded
        if (isset($this->data['image']) && is_array($this->data['image']) && !empty($this->data['image'][0])) {
            $imagePath = $this->data['image'][0];
            $fullPath = storage_path('app/public/' . $imagePath);
            
            // Check if it's a new upload (not the existing image path)
            if (file_exists($fullPath) && $imagePath !== $user->image_path) {
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
                    $user->image_path,
                    fn() => $fileUploadService->handleUserImage($user, $file)
                );
                
                if ($storedPath) {
                    $user->update(['image_path' => $storedPath]);
                    // Clean up temporary Filament upload
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($imagePath);
                }
            }
        }
    }
}
