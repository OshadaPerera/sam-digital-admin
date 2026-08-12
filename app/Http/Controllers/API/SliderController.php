<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SliderSlide;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    /**
     * Display a listing of slider slides by screen size
     */
    public function index()
    {
        try {
            $screenSize = request()->query('screen_size', 'large');

            // Validate screen_size parameter
            if (!in_array($screenSize, ['small', 'large'])) {
                return Response::jsonResponse(false, 'Invalid screen_size parameter. Must be "small" or "large"', [], 400);
            }

            $slides = SliderSlide::select('id', 'type', 'media_path', 'alt_text', 'screen_size', 'order')
                ->where('status', 'active')
                ->where('screen_size', $screenSize)
                ->orderBy('order')
                ->get()
                ->map(function ($slide) {
                    $slide->media_url = $slide->media_path ? Storage::url($slide->media_path) : null;
                    return $slide;
                });

            return Response::jsonResponse(true, 'Slides retrieved successfully', [
                'slides' => $slides,
                'screen_size' => $screenSize,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error retrieving slider slides: '.$e->getMessage());

            return Response::jsonResponse(false, 'Failed to retrieve slides', [
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified slide
     */
    public function show(string $id)
    {
        try {
            $slide = SliderSlide::where('id', $id)
                ->where('status', 'active')
                ->first(['id', 'type', 'media_path', 'alt_text', 'screen_size', 'order', 'created_at']);

            if (!$slide) {
                return Response::jsonResponse(false, 'Slide not found', [
                    'error' => 'No active slide found with the given ID',
                ], 404);
            }

            // Add storage URL
            $slide->media_url = $slide->media_path ? Storage::url($slide->media_path) : null;

            // Add metadata
            $slide->metadata = [
                'formatted_date' => $slide->formatted_date,
                'screen_size' => $slide->screen_size,
                'status' => $slide->status,
            ];

            return Response::jsonResponse(true, 'Slide details retrieved successfully', [
                'slide' => $slide,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error retrieving slide details: '.$e->getMessage());

            return Response::jsonResponse(false, 'Failed to retrieve slide details', [
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}