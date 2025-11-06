<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class ReviewController extends Controller
{
    public function index()
    {
        try {
            $reviews = Review::select('id', 'name', 'message')->where('status', 'active')->get();

            return Response::jsonResponse(true, 'Reviews retrieved successfully', [
                'reviews' => $reviews,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error retrieving reviews: '.$e->getMessage());

            return Response::jsonResponse(false, 'Failed to retrieve reviews', [
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'message' => 'required|string',
            ]);

            $review = Review::create([
                'name' => $request->name,
                'message' => $request->message,
                'status' => 'active',
            ]);

            return Response::jsonResponse(true, 'Review added successfully', [
                'review' => $review,
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Error creating review: '.$e->getMessage());

            return Response::jsonResponse(false, $e->getMessage(), [
                'errors' => $e instanceof \Illuminate\Validation\ValidationException ? $e->errors() : null,
            ], 422);
        }
    }
}
