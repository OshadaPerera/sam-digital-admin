<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class VideoController extends Controller
{
    /**
     * Display a listing of videos
     */
    public function index(Request $request)
    {
        $status = $request->input('status', 'all');
        
        $query = Video::query();
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        $videos = $query->orderBy('created_at', 'desc')->get();

        return view('videos.index', compact('videos', 'status'));
    }

    /**
     * Show the form for creating a new video
     */
    public function create()
    {
        return view('videos.create');
    }

    /**
     * Store a newly created video
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'url' => 'required|url|max:500',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return Response::jsonResponse(false, ucwords($validator->errors()->first()), ['errors' => $validator->errors()], 422);
        }

        try {
            $video = Video::create($request->only(['title', 'url', 'status']));

            return Response::jsonResponse(true, 'Video created successfully', [
                'redirect' => route('videos.index')
            ]);
        } catch (\Exception $e) {
            return Response::jsonResponse(false, ucwords($e->getMessage()), [], 500);
        }
    }

    /**
     * Display the specified video
     */
    public function show(Video $video)
    {
        return view('videos.show', compact('video'));
    }

    /**
     * Show the form for editing the specified video
     */
    public function edit(Video $video)
    {
        return view('videos.edit', compact('video'));
    }

    /**
     * Update the specified video
     */
    public function update(Request $request, Video $video)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'url' => 'required|url|max:500',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return Response::jsonResponse(false, ucwords($validator->errors()->first()), ['errors' => $validator->errors()], 422);
        }

        try {
            $video->update($request->only(['title', 'url', 'status']));

            return Response::jsonResponse(true, 'Video updated successfully', [
                'redirect' => route('videos.index')
            ]);
        } catch (\Exception $e) {
            return Response::jsonResponse(false, ucwords($e->getMessage()), [], 500);
        }
    }

    /**
     * Remove the specified video
     */
    public function destroy(Video $video)
    {
        try {
            $video->delete();
            return Response::jsonResponse(true, 'Video deleted successfully', ['redirect' => route('videos.index')]);
        } catch (\Exception $e) {
            return Response::jsonResponse(false, ucwords($e->getMessage()), [], 500);
        }
    }

    /**
     * Activate a video
     */
    public function activate(Video $video)
    {
        try {
            $video->update(['status' => 'active']);
            return Response::jsonResponse(true, 'Video activated successfully', ['redirect' => route('videos.index')]);
        } catch (\Exception $e) {
            return Response::jsonResponse(false, ucwords($e->getMessage()), [], 500);
        }
    }

    /**
     * Deactivate a video
     */
    public function deactivate(Video $video)
    {
        try {
            $video->update(['status' => 'inactive']);
            return Response::jsonResponse(true, 'Video deactivated successfully', ['redirect' => route('videos.index')]);
        } catch (\Exception $e) {
            return Response::jsonResponse(false, ucwords($e->getMessage()), [], 500);
        }
    }
}
