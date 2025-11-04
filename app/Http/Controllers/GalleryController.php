<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\AlbumImage;
use App\Services\AlbumService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class GalleryController extends Controller
{
    protected $albumService;

    public function __construct(AlbumService $albumService)
    {
        $this->albumService = $albumService;
    }

    /**
     * Display a listing of Gallery albums
     */
    public function index(Request $request)
    {
        $status = $request->input('status', 'all');
        $albums = $this->albumService->getAlbumsByType('gallery', $status);

        return view('gallery.index', compact('albums', 'status'));
    }

    /**
     * Show the form for creating a new album
     */
    public function create()
    {
        return view('gallery.create');
    }

    /**
     * Store a newly created album
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return Response::jsonResponse(false, ucwords($validator->errors()->first()), ['errors' => $validator->errors()], 422);
        }

        try {
            $album = $this->albumService->createAlbum(
                $request->only(['title', 'description', 'status']),
                'gallery',
                $request->file('cover_image')
            );

            return Response::jsonResponse(true, 'Album created successfully', [
                'redirect' => route('gallery.show', $album)
            ]);
        } catch (\Exception $e) {
            return Response::jsonResponse(false, ucwords($e->getMessage()), [], 500);
        }
    }

    /**
     * Display the specified album
     */
    public function show(Album $gallery)
    {
        $album = $this->albumService->getAlbumWithImages($gallery->id);
        return view('gallery.show', compact('album'));
    }

    /**
     * Show the form for editing the specified album
     */
    public function edit(Album $gallery)
    {
        return view('gallery.edit', ['album' => $gallery]);
    }

    /**
     * Update the specified album
     */
    public function update(Request $request, Album $gallery)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return Response::jsonResponse(false, ucwords($validator->errors()->first()), ['errors' => $validator->errors()], 422);
        }

        try {
            $this->albumService->updateAlbum(
                $gallery,
                $request->only(['title', 'description', 'status']),
                $request->file('cover_image')
            );

            return Response::jsonResponse(true, 'Album updated successfully', [
                'redirect' => route('gallery.show', $gallery)
            ]);
        } catch (\Exception $e) {
            return Response::jsonResponse(false, ucwords($e->getMessage()), [], 500);
        }
    }

    /**
     * Remove the specified album
     */
    public function destroy(Album $gallery)
    {
        try {
            $this->albumService->deleteAlbum($gallery);
            return Response::jsonResponse(true, 'Album deleted successfully', ['redirect' => route('gallery.index')]);
        } catch (\Exception $e) {
            return Response::jsonResponse(false, ucwords($e->getMessage()), [], 500);
        }
    }

    /**
     * Activate an album
     */
    public function activate(Album $gallery)
    {
        try {
            $this->albumService->updateStatus($gallery, 'active');
            return Response::jsonResponse(true, 'Album activated successfully', ['redirect' => route('gallery.index')]);
        } catch (\Exception $e) {
            return Response::jsonResponse(false, ucwords($e->getMessage()), [], 500);
        }
    }

    /**
     * Deactivate an album
     */
    public function deactivate(Album $gallery)
    {
        try {
            $this->albumService->updateStatus($gallery, 'inactive');
            return Response::jsonResponse(true, 'Album deactivated successfully', ['redirect' => route('gallery.index')]);
        } catch (\Exception $e) {
            return Response::jsonResponse(false, ucwords($e->getMessage()), [], 500);
        }
    }

    /**
     * Add images to an album
     */
    public function addImages(Request $request, Album $gallery)
    {
        $validator = Validator::make($request->all(), [
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return Response::jsonResponse(false, ucwords($validator->errors()->first()), ['errors' => $validator->errors()], 422);
        }

        try {
            $this->albumService->addImagesToAlbum($gallery, $request->file('images'));
            return Response::jsonResponse(true, 'Images added successfully', ['redirect' => route('gallery.show', $gallery)]);
        } catch (\Exception $e) {
            return Response::jsonResponse(false, ucwords($e->getMessage()), [], 500);
        }
    }

    /**
     * Delete a single image
     */
    public function deleteImage(Album $gallery, AlbumImage $image)
    {
        try {
            if ($image->album_id !== $gallery->id) {
                return Response::jsonResponse(false, 'Image does not belong to this album', [], 403);
            }

            $this->albumService->deleteImage($image);
            return Response::jsonResponse(true, 'Image deleted successfully', ['redirect' => route('gallery.show', $gallery)]);
        } catch (\Exception $e) {
            return Response::jsonResponse(false, ucwords($e->getMessage()), [], 500);
        }
    }
}
