<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Album;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $albums = Album::select('id', 'title', 'description', 'cover_image', 'created_at')
                ->where('status', 'active')
                ->where('type', 'gallery')
                ->withCount('images')
                ->orderBy('created_at', 'desc') // Add ordering for consistent results
                ->get();

            return Response::jsonResponse(true, 'Albums retrieved successfully', [
                'albums' => $albums,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error retrieving albums: '.$e->getMessage());

            return Response::jsonResponse(false, 'Failed to retrieve albums', [
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $album = Album::with(['images' => function ($query) {
                $query->select('id', 'album_id', 'image_path', 'caption')
                    ->where('status', 'active')
                    ->orderBy('created_at', 'desc'); // Add ordering for consistent results
            }])
                ->where('id', $id)
                ->where('status', 'active')
                ->where('type', 'gallery')
                ->firstOrFail(['id', 'title', 'description', 'cover_image', 'created_at']);

            return Response::jsonResponse(true, 'Album details retrieved successfully', [
                'album' => $album,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error retrieving album details: '.$e->getMessage());

            return Response::jsonResponse(false, 'Failed to retrieve album details', [
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
