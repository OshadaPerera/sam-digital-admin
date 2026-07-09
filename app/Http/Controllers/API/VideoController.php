<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class VideoController extends Controller
{
    /**
     * Display a listing of active videos
     */
    public function index()
    {
        try {
            $videos = Video::select('id', 'title', 'url', 'created_at')
                ->where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->get();

            return Response::jsonResponse(true, 'Videos retrieved successfully', [
                'videos' => $videos,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error retrieving videos: '.$e->getMessage());

            return Response::jsonResponse(false, 'Failed to retrieve videos', [
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified video
     */
    public function show(string $id)
    {
        try {
            $video = Video::where('id', $id)
                ->where('status', 'active')
                ->first(['id', 'title', 'url', 'created_at']);

            if (!$video) {
                return Response::jsonResponse(false, 'Video not found', [
                    'error' => 'No active video found with the given ID',
                ], 404);
            }

            return Response::jsonResponse(true, 'Video details retrieved successfully', [
                'video' => $video,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error retrieving video details: '.$e->getMessage());

            return Response::jsonResponse(false, 'Failed to retrieve video details', [
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
