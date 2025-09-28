<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait HandlesFileUpload
{
    /**
     * Upload a file and return the path.
     *
     * @param  UploadedFile|string|null  $file
     * @param  string  $directory
     * @return string|null
     */
    public function uploadFile($file, string $directory): ?string
    {
        if (!$file instanceof UploadedFile) {
            return null;
        }

        // Store in public disk (storage/app/public)
        return $file->store($directory, 'public');
    }

    /**
     * Upload a file into a specific subdirectory, optionally cleaning the directory first.
     * Returns the stored relative path on the public disk.
     */
    public function uploadFileToDirectory(UploadedFile $file, string $directory, bool $cleanDirectoryFirst = false): ?string
    {
        if ($cleanDirectoryFirst) {
            $this->deleteAllFilesInDirectory($directory);
        }

        return $file->store($directory, 'public');
    }

    /**
     * Upload multiple files to a directory. Returns array of stored relative paths.
     */
    public function uploadMultipleFilesToDirectory(array $files, string $directory): array
    {
        $storedPaths = [];
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $storedPaths[] = $file->store($directory, 'public');
            }
        }

        return $storedPaths;
    }

    /**
     * Store an uploaded file under a directory with a specific filename.
     * Returns the stored relative path on the public disk.
     */
    public function uploadFileWithFixedName(UploadedFile $file, string $directory, string $filename): ?string
    {
        // Ensure destination directory exists
        Storage::disk('public')->makeDirectory($directory);

        $path = rtrim($directory, '/').'/'.$filename;
        $stored = Storage::disk('public')->putFileAs($directory, $file, $filename);

        return $stored ? $path : null;
    }

    /**
     * Delete a file from storage.
     *
     * @param  string|null  $path
     * @return bool
     */
    public function deleteFile(?string $path): bool
    {
        if ($path && Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }

        return false;
    }

    /**
     * Delete all files within a directory on the public disk.
     */
    public function deleteAllFilesInDirectory(string $directory): void
    {
        if (Storage::disk('public')->exists($directory)) {
            $files = Storage::disk('public')->files($directory);
            if (!empty($files)) {
                Storage::disk('public')->delete($files);
            }
        }
    }

    /**
     * Move a file to a new directory, preserving the basename. Returns the new path.
     */
    public function moveFileToDirectory(string $currentPath, string $newDirectory): ?string
    {
        if (!$currentPath || !Storage::disk('public')->exists($currentPath)) {
            return null;
        }

        $basename = basename($currentPath);
        $newPath = trim($newDirectory, '/').'/'.$basename;

        // Ensure destination directory exists
        Storage::disk('public')->makeDirectory($newDirectory);

        if (Storage::disk('public')->move($currentPath, $newPath)) {
            return $newPath;
        }

        return null;
    }
}
