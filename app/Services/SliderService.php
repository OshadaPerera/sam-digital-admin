<?php

namespace App\Services;

use App\Models\SliderSlide;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SliderService
{
    protected $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    /**
     * Get all slides by screen size with optional status filter
     */
    public function getSlidesByScreenSize(string $screenSize, ?string $status = 'all')
    {
        return SliderSlide::ofScreenSize($screenSize)
            ->when($status !== 'all', function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->ordered()
            ->get();
    }

    /**
     * Get all slides with optional status filter
     */
    public function getAllSlides(?string $status = 'all')
    {
        return SliderSlide::when($status !== 'all', function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->ordered()
            ->get();
    }

    /**
     * Get a single slide by ID
     */
    public function getSlideById(int $slideId)
    {
        return SliderSlide::findOrFail($slideId);
    }

    /**
     * Create a new slide
     */
    public function createSlide(array $data, ?UploadedFile $mediaFile = null)
    {
        DB::beginTransaction();
        try {
            // Handle media file upload
            if ($mediaFile) {
                if ($data['type'] === 'image') {
                    $data['media_path'] = $this->mediaService->processAlbumImage($mediaFile, 'slider/images');
                } elseif ($data['type'] === 'video') {
                    $data['media_path'] = $this->mediaService->processVideoFile($mediaFile, 'slider/videos');
                }
            }

            // Set order if not provided
            if (!isset($data['order'])) {
                $maxOrder = SliderSlide::max('order') ?? 0;
                $data['order'] = $maxOrder + 1;
            }

            $slide = SliderSlide::create($data);

            DB::commit();

            return $slide;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create slider slide', [
                'data' => $data,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            // Delete uploaded media if slide creation fails
            if (isset($data['media_path'])) {
                $this->mediaService->delete($data['media_path']);
            }
            throw $e;
        }
    }

    /**
     * Update a slide
     */
    public function updateSlide(SliderSlide $slide, array $data, ?UploadedFile $mediaFile = null)
    {
        DB::beginTransaction();
        try {
            $oldMediaPath = $slide->media_path;

            // Handle media file upload
            if ($mediaFile) {
                if ($data['type'] === 'image') {
                    $data['media_path'] = $this->mediaService->processAlbumImage($mediaFile, 'slider/images');
                } elseif ($data['type'] === 'video') {
                    $data['media_path'] = $this->mediaService->processVideoFile($mediaFile, 'slider/videos');
                }
            }

            $slide->update($data);

            // Delete old media if a new one was uploaded
            if ($mediaFile && $oldMediaPath) {
                $this->mediaService->delete($oldMediaPath);
            }

            DB::commit();

            return $slide;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update slider slide', [
                'slide_id' => $slide->id,
                'data' => $data,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            // Delete uploaded media if update fails
            if (isset($data['media_path']) && $data['media_path'] !== $oldMediaPath) {
                $this->mediaService->delete($data['media_path']);
            }
            throw $e;
        }
    }

    /**
     * Delete a slide
     */
    public function deleteSlide(SliderSlide $slide)
    {
        DB::beginTransaction();
        try {
            // Delete media file
            if ($slide->media_path) {
                $this->mediaService->delete($slide->media_path);
            }

            $slide->delete();

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete slider slide', [
                'slide_id' => $slide->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Update slide status
     */
    public function updateStatus(SliderSlide $slide, string $status)
    {
        return $slide->update(['status' => $status]);
    }

    /**
     * Reorder slides
     */
    public function reorderSlides(array $slideOrders)
    {
        DB::beginTransaction();
        try {
            foreach ($slideOrders as $slideId => $order) {
                SliderSlide::where('id', $slideId)->update(['order' => $order]);
            }

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to reorder slider slides', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}