<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\Category;
use App\Models\LiveChannel;
use App\Models\Video;
use App\Models\Slider;

class DashboardController extends Controller
{
    public function index()
    {
        // Totals for the dashboard
        $totalVideos = Video::count();
        $totalLiveChannels = LiveChannel::count();
        $totalCategories = Category::count();
        $totalSliders = Slider::count();
        $totalAds = Ad::count();
        $activeAds = Ad::where('status', true)->count();
        
        // Recent videos for activity feed
        $recentVideos = Video::with('category')->latest()->take(10)->get();

        // Recent live channels
        $recentChannels = LiveChannel::latest()->take(3)->get();
        
        // Recent ads
        $recentAds = Ad::latest()->take(5)->get();

        // Chart data for content distribution
        $categories = Category::withCount('videos')->get();
        $chartVideosLabels = $categories->pluck('name');
        $chartVideosData = $categories->pluck('videos_count');

        return view('admin.dashboard', compact(
            'totalVideos',
            'totalLiveChannels', 
            'totalCategories',
            'totalSliders',
            'totalAds',
            'activeAds',
            'recentVideos',
            'recentChannels',
            'recentAds',
            'chartVideosLabels',
            'chartVideosData'
        ));
    }
}
