<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Album;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

abstract class BaseAlbumController extends Controller
{
    /**
     * Get the album type for this controller
     */
    abstract protected function getAlbumType(): string;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $page = request()->query('page', 1);
            $perPage = request()->query('per_page', 12);
            $search = request()->query('search', '');

            $query = Album::select('id', 'title', 'description', 'cover_image', 'created_at')
                ->where('status', 'active')
                ->where('type', $this->getAlbumType())
                ->withCount('images');

            // Apply search filter if provided
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%')
                      ->orWhere('description', 'like', '%' . $search . '%');
                });
            }

            $albums = $query->orderBy('created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page)
                ->through(function ($album) {
                    $album->cover_image_url = $album->cover_image ? Storage::url($album->cover_image) : null;
                    return $album;
                });

            return Response::jsonResponse(true, 'Albums retrieved successfully', [
                'albums' => $albums->items(),
                'pagination' => [
                    'current_page' => $albums->currentPage(),
                    'per_page' => $albums->perPage(),
                    'total' => $albums->total(),
                    'last_page' => $albums->lastPage(),
                    'from' => $albums->firstItem(),
                    'to' => $albums->lastItem(),
                ],
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
                $query->select('id', 'album_id', 'image_path', 'order', 'created_at')
                    ->orderBy('order')
                    ->orderBy('created_at', 'desc');
            }])
                ->where('id', $id)
                ->where('status', 'active')
                ->where('type', $this->getAlbumType())
                ->first(['id', 'title', 'description', 'cover_image', 'created_at']);

            if (!$album) {
                return Response::jsonResponse(false, 'Album not found', [
                    'error' => 'No active ' . $this->getAlbumType() . ' album found with the given ID',
                ], 404);
            }

            // Add storage URLs
            $album->cover_image_url = $album->cover_image ? Storage::url($album->cover_image) : null;
            $album->images->transform(function ($image) {
                $image->image_url = $image->image_path ? Storage::url($image->image_path) : null;

                return $image;
            });

            // Add metadata
            $album->metadata = [
                'formatted_date' => $album->formatted_date,
                'images_count' => $album->images_count,
                'type' => $album->type,
                'status' => $album->status,
            ];

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
