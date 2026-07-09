<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Review;
use App\Models\User;
use App\Models\Video;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with statistics
     */
    public function index()
    {
        $stats = [];

        // Reviews count
        if (auth()->user()->can('view reviews')) {
            $stats['reviews'] = [
                'title' => 'Total Reviews',
                'count' => Review::count(),
                'icon' => 'star',
                'color' => 'blue',
                'active_count' => Review::where('status', 'active')->count(),
            ];
        }

        // Videos count
        if (auth()->user()->can('view videos')) {
            $stats['videos'] = [
                'title' => 'Total Videos',
                'count' => Video::count(),
                'icon' => 'video',
                'color' => 'purple',
                'active_count' => Video::where('status', 'active')->count(),
            ];
        }

        // Gallery albums count
        if (auth()->user()->can('view gallery')) {
            $stats['gallery'] = [
                'title' => 'Gallery Albums',
                'count' => Album::where('type', 'gallery')->count(),
                'icon' => 'image',
                'color' => 'green',
                'active_count' => Album::where('type', 'gallery')->where('status', 'active')->count(),
            ];
        }

        // What We Do albums count
        if (auth()->user()->can('view what we do')) {
            $stats['what_we_do'] = [
                'title' => 'What We Do Albums',
                'count' => Album::where('type', 'whatwedo')->count(),
                'icon' => 'briefcase',
                'color' => 'orange',
                'active_count' => Album::where('type', 'whatwedo')->where('status', 'active')->count(),
            ];
        }

        // Users count
        if (auth()->user()->can('view users')) {
            $stats['users'] = [
                'title' => 'Total Users',
                'count' => User::where('id', '!=', 1)->count(),
                'icon' => 'users',
                'color' => 'indigo',
                'active_count' => User::where('id', '!=', 1)->where('is_active', true)->count(),
            ];
        }

        return view('dashboard', compact('stats'));
    }
}
