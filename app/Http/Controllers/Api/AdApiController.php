<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ad;

class AdApiController extends Controller
{
    /**
     * GET /api/ads
     */
    public function index()
    {
        $ads = Ad::query()
            ->select('id', 'title', 'image', 'ad_type', 'position', 'target_url', 'status')
            ->where('status', 1)
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($ad) {
                $imagePath = $ad->image ? ltrim($ad->image, '/') : null;
                $imagePath = $imagePath ? ltrim($imagePath, 'storage/') : null;
                
                return [
                    'id' => $ad->id,
                    'title' => $ad->title,
                    'image' => $ad->image,
                    'ad_type' => $ad->ad_type ?: 'banner',
                    'position' => $ad->position ?: 'home_bottom',
                    'target_url' => $ad->target_url,
                    'status' => $ad->status ? 1 : 0,
                ];
            });

        return response()->json($ads);
    }

    /**
     * GET /api/ads/{position}
     */
    public function byPosition($position)
    {
        $ads = Ad::query()
            ->select('id', 'title', 'image', 'ad_type', 'position', 'target_url', 'status')
            ->where('status', 1)
            ->where('position', $position)
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($ad) {
                $imagePath = $ad->image ? ltrim($ad->image, '/') : null;
                $imagePath = $imagePath ? ltrim($imagePath, 'storage/') : null;
                
                return [
                    'id' => $ad->id,
                    'title' => $ad->title,
                    'image' => $ad->image,
                    'ad_type' => $ad->ad_type ?: 'banner',
                    'position' => $ad->position,
                    'target_url' => $ad->target_url,
                    'status' => $ad->status ? 1 : 0,
                ];
            });

        return response()->json($ads);
    }
}
