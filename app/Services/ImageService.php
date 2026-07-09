<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class ImageService
{
    /**
     * Process and save an image: convert to WebP and compress
     *
     * @param UploadedFile $file
     * @param string $directory Directory path within the public disk
     * @param int $quality WebP quality (0-100, default 80)
     * @param int|null $maxWidth Maximum width (maintains aspect ratio)
     * @param int|null $maxHeight Maximum height (maintains aspect ratio)
     * @return string Path to the saved image
     */
    public function processAndSave(
        UploadedFile $file,
        string $directory,
        int $quality = 80,
        ?int $maxWidth = null,
        ?int $maxHeight = null
    ): string {
        try {
            // Generate a unique filename
            $filename = uniqid() . '_' . time() . '.webp';
            $path = $directory . '/' . $filename;

            // Read the uploaded image
            $image = Image::read($file->getRealPath());

            // Resize if dimensions are specified
            if ($maxWidth || $maxHeight) {
                $image->scale(
                    width: $maxWidth,
                    height: $maxHeight
                );
            }

            // Convert to WebP and encode with compression
            $encodedImage = $image->toWebp($quality);

            // Save to storage
            Storage::disk('public')->put($path, (string) $encodedImage);

            return $path;
        } catch (\Exception $e) {
            Log::error('Failed to process and save image', [
                'filename' => $file->getClientOriginalName(),
                'directory' => $directory,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Process and save a cover image (smaller size)
     *
     * @param UploadedFile $file
     * @param string $directory
     * @return string
     */
    public function processCoverImage(UploadedFile $file, string $directory = 'albums/covers'): string
    {
        return $this->processAndSave(
            file: $file,
            directory: $directory,
            quality: config('image.cover_image.quality', 85),
            maxWidth: config('image.cover_image.max_width', 1200),
            maxHeight: config('image.cover_image.max_height', 800)
        );
    }

    /**
     * Process and save an album image
     *
     * @param UploadedFile $file
     * @param string $directory
     * @return string
     */
    public function processAlbumImage(UploadedFile $file, string $directory = 'albums/images'): string
    {
        return $this->processAndSave(
            file: $file,
            directory: $directory,
            quality: config('image.album_image.quality', 80),
            maxWidth: config('image.album_image.max_width', 1920),
            maxHeight: config('image.album_image.max_height', 1080)
        );
    }

    /**
     * Delete an image from storage
     *
     * @param string $path
     * @return bool
     */
    public function delete(string $path): bool
    {
        try {
            if (Storage::disk('public')->exists($path)) {
                $result = Storage::disk('public')->delete($path);
                return $result;
            }

            Log::warning('Attempted to delete non-existent image', ['path' => $path]);
            return false;
        } catch (\Exception $e) {
            Log::error('Failed to delete image', [
                'path' => $path,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Create a thumbnail from an image
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param int $width
     * @param int $height
     * @return string
     */
    public function createThumbnail(
        UploadedFile $file,
        string $directory,
        int $width = null,
        int $height = null
    ): string {
        try {
            $width = $width ?? config('image.thumbnail.width', 300);
            $height = $height ?? config('image.thumbnail.height', 300);
            $quality = config('image.thumbnail.quality', 75);

            $filename = 'thumb_' . uniqid() . '_' . time() . '.webp';
            $path = $directory . '/' . $filename;

            // Create thumbnail with cover crop
            $image = Image::read($file->getRealPath())
                ->cover($width, $height)
                ->toWebp($quality);

            Storage::disk('public')->put($path, (string) $image);

            return $path;
        } catch (\Exception $e) {
            Log::error('Failed to create thumbnail', [
                'filename' => $file->getClientOriginalName(),
                'directory' => $directory,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
