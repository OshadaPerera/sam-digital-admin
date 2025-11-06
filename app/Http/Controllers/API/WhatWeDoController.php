<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Album;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class WhatWeDoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $albums = Album::select('id', 'title', 'description', 'cover_image', 'created_at')
                ->where('status', 'active')
                ->where('type', 'whatwedo')
                ->withCount('images')
                ->orderBy('created_at', 'desc') // Add ordering for consistent results
                ->get()
                ->map(function ($album) {
                    $album->cover_image_url = $album->cover_image ? Storage::url($album->cover_image) : null;

                    return $album;
                });

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
                $query->select('id', 'album_id', 'image_path', 'order', 'created_at')
                    ->orderBy('order')
                    ->orderBy('created_at', 'desc');
            }])
                ->where('id', $id)
                ->where('status', 'active')
                ->where('type', 'whatwedo')
                ->first(['id', 'title', 'description', 'cover_image', 'created_at']);

            if (!$album) {
                return Response::jsonResponse(false, 'Album not found', [
                    'error' => 'No active whatwedo album found with the given ID',
                ], 404);
            }

            // Add storage URLs
            $album->cover_image_url = $album->cover_image ? Storage::url($album->cover_image) : null;
            $album->images->transform(function ($image) {
                $image->image_url = $image->image_path ? Storage::url($image->image_path) : null;

                return $image;
            });

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
