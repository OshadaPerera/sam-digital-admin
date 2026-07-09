<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ReviewController extends Controller
{
    public function index(Request $request)
    {

        // Get the status filter from the request
        $status = $request->input('status', 'all');

        // Query reviews based on the status filter
        $reviews = Review::when($status !== 'all', function ($query) use ($status) {
            return $query->where('status', $status);
        })->get();

        return view('reviews.index', compact('reviews', 'status'));
    }

    public function destroy(Review $review)
    {

        $review->delete();

        return Response::jsonResponse(true, 'Review deleted successfully', ['redirect' => route('reviews.index')]);
    }

    public function activate(Review $review)
    {

        $review->update(['status' => 'active']);

        return Response::jsonResponse(true, 'Review activated successfully', ['redirect' => route('reviews.index')]);
    }

    public function deactivate(Review $review)
    {

        $review->update(['status' => 'inactive']);

        return Response::jsonResponse(true, 'Review deactivated successfully', ['redirect' => route('reviews.index')]);
    }
}
