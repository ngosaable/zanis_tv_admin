<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Models\Category;
use Illuminate\Http\Request;

class VideoApiController extends Controller
{
    /**
     * Get all movies (videos) - Flutter expected format
     * GET /api/movies
     */
    public function movies(Request $request)
    {
        $query = Video::query()
            ->with(['category:id,name'])
            ->where('status', 1)
            ->orderByDesc('created_at');

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Include videos that are ready for streaming OR have a video file
        $query->where(function ($q) {
            $q->where('hls_conversion_status', 'completed')
              ->orWhereNotNull('video_path');
        });

        $videos = $query->get()->map(function ($video) {
            // Determine video_type and set appropriate URL
            $hasHls = $video->hls_conversion_status === 'completed' && $video->hls_playlist_path;
            
            return [
                "id" => $video->id,
                "category_id" => $video->category_id,
                "title" => $video->title,
                "description" => $video->description,
                
                // Images (Flutter expects relative paths)
                "poster" => $video->poster,
                "thumbnail" => $video->thumbnail,
                
                // Video type and URLs
                "video_type" => $hasHls ? "url" : "upload",
                "video_url" => $hasHls ? $video->hls_playlist_full_url : null,
                "video_path" => $hasHls ? null : $video->video_path,
                
                // Metadata
                "duration" => $video->duration,
                "formatted_duration" => $video->formatted_duration,
                "resolution" => $video->resolution,
                
                "status" => $video->status ? 1 : 0,
                "created_at" => $video->created_at->toISOString(),
            ];
        });

        return response()->json($videos);
    }

    /**
     * Get single movie - Flutter expected format
     * GET /api/movies/{id}
     */
    public function showMovie($id)
    {
        $video = Video::with(['category:id,name'])
            ->where('id', $id)
            ->where('status', 1)
            ->first();

        if (!$video) {
            return response()->json(["error" => "Video not found"], 404);
        }

        $hasHls = $video->hls_conversion_status === 'completed' && $video->hls_playlist_path;

        return response()->json([
            "id" => $video->id,
            "category_id" => $video->category_id,
            "title" => $video->title,
            "description" => $video->description,
            "poster" => $video->poster,
            "thumbnail" => $video->thumbnail,
            "video_type" => $hasHls ? "url" : "upload",
            "video_url" => $hasHls ? $video->hls_playlist_full_url : null,
            "video_path" => $hasHls ? null : $video->video_path,
            "duration" => $video->duration,
            "formatted_duration" => $video->formatted_duration,
            "resolution" => $video->resolution,
            "status" => $video->status ? 1 : 0,
            "created_at" => $video->created_at->toISOString(),
        ]);
    }

    /**
     * Get videos by category
     */
    public function byCategory($categoryId)
    {
        $category = Category::where('id', $categoryId)->where('status', 1)->first();
        
        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        $videos = Video::with(['category:id,name'])
            ->where('category_id', $categoryId)
            ->where('status', 1)
            ->where('hls_conversion_status', 'completed')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($video) {
                $hasHls = $video->hls_conversion_status === 'completed' && $video->hls_playlist_path;
                
                return [
                    "id" => $video->id,
                    "category_id" => $video->category_id,
                    "title" => $video->title,
                    "description" => $video->description,
                    "poster" => $video->poster,
                    "thumbnail" => $video->thumbnail,
                    "video_type" => $hasHls ? "url" : "upload",
                    "video_url" => $hasHls ? $video->hls_playlist_full_url : null,
                    "video_path" => $hasHls ? null : $video->video_path,
                    "duration" => $video->duration,
                    "formatted_duration" => $video->formatted_duration,
                    "status" => $video->status ? 1 : 0,
                ];
            });

        return response()->json([
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
            ],
            'videos' => $videos
        ]);
    }

    /**
     * Get latest videos
     */
    public function latest(Request $request)
    {
        $limit = $request->get('limit', 10);
        
        $videos = Video::with(['category:id,name'])
            ->where('status', 1)
            ->where('hls_conversion_status', 'completed')
            ->orderByDesc('created_at')
            ->take($limit)
            ->get()
            ->map(function ($video) {
                return [
                    "id" => $video->id,
                    "category_id" => $video->category_id,
                    "title" => $video->title,
                    "poster" => $video->poster,
                    "video_type" => "url",
                    "video_url" => $video->hls_playlist_full_url,
                    "duration" => $video->duration,
                    "status" => $video->status ? 1 : 0,
                ];
            });

        return response()->json($videos);
    }

    /**
     * Legacy: Get all videos (original format)
     */
    public function index(Request $request)
    {
        return $this->movies($request);
    }

    /**
     * Legacy: Get single video
     */
    public function show($id)
    {
        return $this->showMovie($id);
    }
}
