<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class MediaService
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
        int $quality = 70,
        ?int $maxWidth = null,
        ?int $maxHeight = null
    ): string {
        try {
            // Ensure directory exists
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory, 0755, true);
            }

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
            throw new \Exception('Failed to process image: ' . $e->getMessage(), 0, $e);
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
            quality: config('image.cover_image.quality', 75),
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
            quality: config('image.album_image.quality', 70),
            maxWidth: config('image.album_image.max_width', 1920),
            maxHeight: config('image.album_image.max_height', 1080)
        );
    }

    /**
     * Delete a media file from storage (image or video)
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

            Log::warning('Attempted to delete non-existent media file', ['path' => $path]);
            return false;
        } catch (\Exception $e) {
            Log::error('Failed to delete media file', [
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
            // Ensure directory exists
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory, 0755, true);
            }

            $width = $width ?? config('image.thumbnail.width', 300);
            $height = $height ?? config('image.thumbnail.height', 300);
            $quality = config('image.thumbnail.quality', 70);

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
            throw new \Exception('Failed to create thumbnail: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Process and save a video file
     *
     * @param UploadedFile $file
     * @param string $directory Directory path within the public disk
     * @return string Path to the saved video
     */
    public function processVideoFile(UploadedFile $file, string $directory = 'videos'): string
    {
        try {
            // Ensure directory exists
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory, 0755, true);
            }

            // Validate video file
            $allowedMimes = ['video/mp4', 'video/webm', 'video/ogg'];
            if (!in_array($file->getMimeType(), $allowedMimes)) {
                throw new \Exception('Invalid video file type. Allowed types: mp4, webm, ogg');
            }

            // Validate file size (max 25MB)
            $maxSize = 25 * 1024 * 1024; // 25MB
            if ($file->getSize() > $maxSize) {
                throw new \Exception('Video file size exceeds 25MB limit');
            }

            // Generate a unique filename preserving original extension
            $extension = $file->getClientOriginalExtension();
            $filename = uniqid() . '_' . time() . '.' . $extension;
            $path = $directory . '/' . $filename;

            // Store the video file
            Storage::disk('public')->putFileAs($directory, $file, $filename);

            return $path;
        } catch (\Exception $e) {
            Log::error('Failed to process and save video', [
                'filename' => $file->getClientOriginalName(),
                'directory' => $directory,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Failed to process video: ' . $e->getMessage(), 0, $e);
        }
    }
}
