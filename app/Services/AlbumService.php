<?php

namespace App\Services;

use App\Models\Album;
use App\Models\AlbumImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AlbumService
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Get all albums by type with optional status filter
     */
    public function getAlbumsByType(string $type, ?string $status = 'all')
    {
        return Album::ofType($type)
            ->with('images')
            ->when($status !== 'all', function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->get();
    }

    /**
     * Get a single album with its images
     */
    public function getAlbumWithImages(int $albumId)
    {
        return Album::with('images')->findOrFail($albumId);
    }

    /**
     * Create a new album
     */
    public function createAlbum(array $data, string $type, ?UploadedFile $coverImage = null)
    {
        DB::beginTransaction();
        try {

            // Handle cover image upload with WebP conversion and compression
            if ($coverImage) {
                $data['cover_image'] = $this->imageService->processCoverImage($coverImage);
            }

            $data['type'] = $type;
            $album = Album::create($data);

            DB::commit();

            return $album;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create album', [
                'type' => $type,
                'data' => $data,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            // Delete uploaded cover image if album creation fails
            if (isset($data['cover_image'])) {
                $this->imageService->delete($data['cover_image']);
            }
            throw $e;
        }
    }

    /**
     * Update an album
     */
    public function updateAlbum(Album $album, array $data, ?UploadedFile $coverImage = null)
    {
        DB::beginTransaction();
        try {

            $oldCoverImage = $album->cover_image;

            // Handle cover image upload with WebP conversion and compression
            if ($coverImage) {
                $data['cover_image'] = $this->imageService->processCoverImage($coverImage);
            }

            $album->update($data);

            // Delete old cover image if a new one was uploaded
            if ($coverImage && $oldCoverImage) {
                $this->imageService->delete($oldCoverImage);
            }

            DB::commit();

            return $album;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update album', [
                'album_id' => $album->id,
                'data' => $data,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            // Delete uploaded cover image if update fails
            if (isset($data['cover_image']) && $data['cover_image'] !== $oldCoverImage) {
                $this->imageService->delete($data['cover_image']);
            }
            throw $e;
        }
    }

    /**
     * Delete an album and all its images
     */
    public function deleteAlbum(Album $album)
    {
        DB::beginTransaction();
        try {

            // Delete cover image
            if ($album->cover_image) {
                $this->imageService->delete($album->cover_image);
            }

            // Delete all album images
            foreach ($album->images as $image) {
                $this->imageService->delete($image->image_path);
            }

            $album->delete();

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete album', [
                'album_id' => $album->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Add multiple images to an album
     */
    public function addImagesToAlbum(Album $album, array $images)
    {
        DB::beginTransaction();
        try {
            $uploadedImages = [];
            $currentMaxOrder = $album->images()->max('order') ?? 0;

            foreach ($images as $index => $image) {
                if ($image instanceof UploadedFile) {
                    // Process image: convert to WebP and compress
                    $path = $this->imageService->processAlbumImage($image);
                    $uploadedImages[] = [
                        'album_id' => $album->id,
                        'image_path' => $path,
                        'order' => $currentMaxOrder + $index + 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            AlbumImage::insert($uploadedImages);

            DB::commit();

            return $uploadedImages;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to add images to album', [
                'album_id' => $album->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            // Delete uploaded images if insertion fails
            foreach ($uploadedImages as $image) {
                $this->imageService->delete($image['image_path']);
            }
            throw $e;
        }
    }

    /**
     * Delete a single image from an album
     */
    public function deleteImage(AlbumImage $image)
    {
        DB::beginTransaction();
        try {

            $this->imageService->delete($image->image_path);
            $image->delete();

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete album image', [
                'image_id' => $image->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Update album status
     */
    public function updateStatus(Album $album, string $status)
    {
        return $album->update(['status' => $status]);
    }

    /**
     * Reorder images in an album
     */
    public function reorderImages(Album $album, array $imageOrders)
    {
        DB::beginTransaction();
        try {

            foreach ($imageOrders as $imageId => $order) {
                AlbumImage::where('album_id', $album->id)
                    ->where('id', $imageId)
                    ->update(['order' => $order]);
            }

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to reorder album images', [
                'album_id' => $album->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
