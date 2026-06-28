<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Models\Category;
use App\Jobs\ProcessVideoJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class VideoController extends Controller
{
    public function index()
    {
        $query = Video::with('category')->orderByDesc('created_at');

        $status = request('status');
        
        if ($status == 'active') {
            $query->where('status', 1);
        } elseif ($status == 'inactive') {
            $query->where('status', 0);
        } elseif ($status == 'processing') {
            $query->where('processing_status', 'processing');
        } elseif ($status == 'failed') {
            $query->where('processing_status', 'failed');
        }
        // 'all' or null shows all videos

        $videos = $query->paginate(20)->appends(request()->query());

        return view('admin.videos.index', compact('videos'));
    }

    public function create()
    {
        $categories = Category::where('status', 1)->get();
        return view('admin.videos.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $videoSource = $request->input('video_source', 'upload');
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'convert_to_hls' => 'boolean',
            'hls_quality' => 'nullable|string|in:360p,480p,720p,1080p,adaptive',
            'status' => 'boolean',
        ]);

        if ($videoSource === 'link') {
            $request->validate([
                'video_url' => 'required|url',
            ]);
        } else {
            $request->validate([
                'video' => 'required|file|mimes:mp4,avi,mov,wmv,flv,mkv,webm|max:500000',
            ]);
        }

        // Handle poster upload
        if ($request->hasFile('poster')) {
            $posterPath = $request->file('poster')->store('videos/posters', 'public');
        }

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('videos/thumbnails', 'public');
        }

        // Handle video upload or link
        $videoPath = null;
        $fileSize = null;
        $isExternalUrl = false;
        
        if ($videoSource === 'link') {
            $videoPath = $request->input('video_url');
            $isExternalUrl = true;
        } elseif ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('videos/uploads', 'public');
            $fileSize = $request->file('video')->getSize();
        }

        // Determine HLS quality to convert to
        $convertToHls = $request->boolean('convert_to_hls', true);
        $hlsQuality = $request->input('hls_quality', '480p');

        $video = Video::create([
            'category_id' => $request->category_id,
            'title' => $request->title,
            'description' => $request->description,
            'poster' => $posterPath ?? null,
            'thumbnail' => $thumbnailPath ?? null,
            'video_path' => $videoPath,
            'file_size' => $fileSize,
            'is_external_url' => $isExternalUrl,
            'processing_status' => $convertToHls && !$isExternalUrl ? 'pending' : 'completed',
            'processing_progress' => $convertToHls && !$isExternalUrl ? 0 : 100,
            'hls_conversion_status' => $convertToHls && !$isExternalUrl ? 'pending' : 'completed',
            'status' => $request->boolean('status', true),
        ]);

        // Queue the video processing job if HLS conversion is selected
        if ($convertToHls && $videoPath && !$isExternalUrl) {
            ProcessVideoJob::dispatch($video, $hlsQuality);
            
            $qualityMsg = $hlsQuality === 'adaptive' 
                ? 'Adaptive (Multiple Qualities)' 
                : ucfirst($hlsQuality);
                
            // Return JSON for AJAX requests (from chunked upload)
            if ($this->shouldReturnJson($request)) {
                return response()->json([
                    'success' => true,
                    'video_id' => $video->id,
                    'processing_started' => true,
                    'message' => "Video uploaded! Converting to HLS ({$qualityMsg}).",
                    'redirect' => route('admin.videos.index')
                ]);
            }
                
            return redirect()
                ->route('admin.videos.index')
                ->with('success', "Video uploaded! Converting to HLS ({$qualityMsg}). Processing will begin shortly.");
        }

        if ($this->shouldReturnJson($request)) {
            return response()->json([
                'success' => true,
                'video_id' => $video->id,
                'processing_started' => false,
                'message' => $isExternalUrl ? 'Video link added successfully.' : 'Video uploaded successfully.',
                'redirect' => route('admin.videos.index')
            ]);
        }

        return redirect()
            ->route('admin.videos.index')
            ->with('success', $isExternalUrl ? 'Video link added successfully.' : 'Video uploaded successfully.');
    }

    public function show(Video $video)
    {
        $video->load('category');
        return view('admin.videos.show', compact('video'));
    }

    public function edit(Video $video)
    {
        $categories = Category::where('status', 1)->get();
        return view('admin.videos.edit', compact('video', 'categories'));
    }

    public function update(Request $request, Video $video)
    {
        $videoSource = $request->input('video_source', $video->is_external_url ? 'link' : 'upload');

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'convert_to_hls' => 'boolean',
            'hls_quality' => 'nullable|string|in:360p,480p,720p,1080p,adaptive',
            'status' => 'boolean',
        ]);

        if ($videoSource === 'link') {
            $request->validate([
                'video_url' => 'required|url',
            ]);
        } else {
            $request->validate([
                'video' => ($video->is_external_url ? 'required' : 'nullable') . '|file|mimes:mp4,avi,mov,wmv,flv,mkv,webm|max:500000',
            ]);
        }

        // Handle poster upload
        if ($request->hasFile('poster')) {
            // Delete old poster
            if ($video->poster) {
                Storage::disk('public')->delete($video->poster);
            }
            $posterPath = $request->file('poster')->store('videos/posters', 'public');
            $video->poster = $posterPath;
        }

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail
            if ($video->thumbnail) {
                Storage::disk('public')->delete($video->thumbnail);
            }
            $thumbnailPath = $request->file('thumbnail')->store('videos/thumbnails', 'public');
            $video->thumbnail = $thumbnailPath;
        }

        $convertToHls = $request->boolean('convert_to_hls', true);
        $hlsQuality = $request->input('hls_quality', '480p');
        $shouldQueueProcessing = false;

        $attributes = [
            'category_id' => $request->category_id,
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->boolean('status', $video->status),
        ];

        if ($videoSource === 'link') {
            $this->deleteGeneratedStreams($video);

            if (!$video->is_external_url && $video->video_path) {
                Storage::disk('public')->delete($video->video_path);
            }

            $attributes = array_merge($attributes, [
                'video_path' => $request->input('video_url'),
                'file_size' => null,
                'duration' => null,
                'resolution' => null,
                'is_external_url' => true,
                'processing_status' => 'completed',
                'processing_progress' => 100,
                'processing_log' => null,
                'hls_conversion_status' => 'completed',
                'hls_playlist_path' => null,
                'hls_directory' => null,
                'hls_qualities' => null,
            ]);
        } elseif ($request->hasFile('video')) {
            $this->deleteGeneratedStreams($video);

            if (!$video->is_external_url && $video->video_path) {
                Storage::disk('public')->delete($video->video_path);
            }

            $storedVideoPath = $request->file('video')->store('videos/uploads', 'public');

            $attributes = array_merge($attributes, [
                'video_path' => $storedVideoPath,
                'file_size' => $request->file('video')->getSize(),
                'duration' => null,
                'resolution' => null,
                'is_external_url' => false,
                'processing_status' => $convertToHls ? 'pending' : 'completed',
                'processing_progress' => $convertToHls ? 0 : 100,
                'processing_log' => null,
                'hls_conversion_status' => $convertToHls ? 'pending' : 'completed',
                'hls_playlist_path' => null,
                'hls_directory' => null,
                'hls_qualities' => null,
            ]);

            $shouldQueueProcessing = $convertToHls;
        }

        $video->update($attributes);

        if ($shouldQueueProcessing) {
            ProcessVideoJob::dispatch($video->fresh(), $hlsQuality);
        }

        return redirect()
            ->route('admin.videos.index')
            ->with('success', $shouldQueueProcessing ? 'Video updated. HLS conversion has started.' : 'Video updated successfully.');
    }

    public function destroy(Video $video)
    {
        // Delete associated files
        if ($video->poster) {
            Storage::disk('public')->delete($video->poster);
        }
        if ($video->thumbnail) {
            Storage::disk('public')->delete($video->thumbnail);
        }
        if ($video->video_path && !$video->is_external_url) {
            Storage::disk('public')->delete($video->video_path);
        }
        $this->deleteGeneratedStreams($video);

        $video->delete();

        return redirect()
            ->route('admin.videos.index')
            ->with('success', 'Video deleted successfully.');
    }

    /**
     * Manual trigger for HLS conversion with quality selection
     */
    public function convertToHls(Request $request, Video $video)
    {
        $request->validate([
            'quality' => 'nullable|string|in:360p,480p,720p,1080p,adaptive',
        ]);

        if ($video->hls_conversion_status === 'processing') {
            return redirect()
                ->route('admin.videos.show', $video)
                ->with('error', 'Video is already being processed.');
        }

        if ($video->is_external_url) {
            return redirect()
                ->route('admin.videos.show', $video)
                ->with('error', 'External video URLs cannot be converted to HLS.');
        }

        if (!$video->video_path) {
            return redirect()
                ->route('admin.videos.show', $video)
                ->with('error', 'No video file found to convert.');
        }

        $quality = $request->input('quality', '720p');

        // Reset status and queue the job
        $video->update([
            'processing_status' => 'pending',
            'processing_progress' => 0,
            'hls_conversion_status' => 'pending',
        ]);

        ProcessVideoJob::dispatch($video, $quality);

        $qualityMsg = $quality === 'adaptive' 
            ? 'Adaptive (Multiple Qualities)' 
            : ucfirst($quality);

        return redirect()
            ->route('admin.videos.show', $video)
            ->with('success', "HLS conversion started ({$qualityMsg}).");
    }

    /**
     * Get video processing status (AJAX endpoint)
     */
    public function processingStatus(Video $video)
    {
        $video->refresh();
        
        return response()->json([
            'processing_status' => $video->processing_status,
            'hls_conversion_status' => $video->hls_conversion_status,
            'processing_progress' => $video->processing_progress,
            'is_ready_for_streaming' => $video->isReadyForStreaming(),
            'hls_playlist_url' => $video->hls_playlist_full_url,
            'qualities' => $video->hls_qualities,
            'duration' => $video->formatted_duration,
            'resolution' => $video->resolution,
        ]);
    }

    /**
     * Handle chunk upload for large video files
     */
    public function chunkUpload(Request $request)
    {
        $request->validate([
            'video_chunk' => 'required|file',
            'chunk_index' => 'required|integer',
            'total_chunks' => 'required|integer',
            'original_name' => 'required|string',
        ]);

        $chunkIndex = $request->input('chunk_index');
        $totalChunks = $request->input('total_chunks');
        $originalName = $request->input('original_name');
        
        // Create temp directory for chunks
        $tempDir = 'temp/chunks/' . md5($originalName);
        $chunkPath = $request->file('video_chunk')->storeAs($tempDir, "chunk_{$chunkIndex}", 'public');
        
        return response()->json([
            'success' => true,
            'chunk_index' => $chunkIndex,
            'total_chunks' => $totalChunks,
            'message' => "Chunk {$chunkIndex} uploaded successfully"
        ]);
    }

    /**
     * Merge uploaded chunks into final video file
     */
    public function mergeChunks(Request $request)
    {
        $request->validate([
            'original_name' => 'required|string',
            'total_chunks' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'convert_to_hls' => 'boolean',
            'hls_quality' => 'nullable|string|in:360p,480p,720p,1080p,adaptive',
            'status' => 'boolean',
        ]);

        $originalName = $request->input('original_name');
        $totalChunks = $request->input('total_chunks');
        $tempDir = 'temp/chunks/' . md5($originalName);
        
        // Create final video path
        $finalPath = 'videos/uploads/' . Str::uuid() . '.' . pathinfo($originalName, PATHINFO_EXTENSION);
        $fullPath = Storage::disk('public')->path($finalPath);
        
        // Ensure directory exists
        $directory = dirname($fullPath);
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }
        
        // Open final file for writing
        $outFile = fopen($fullPath, 'wb');
        
        if (!$outFile) {
            return response()->json(['success' => false, 'message' => 'Failed to create output file'], 500);
        }
        
        // Merge all chunks
        for ($i = 0; $i < $totalChunks; $i++) {
            $chunkFile = Storage::disk('public')->path($tempDir . "/chunk_{$i}");
            
            if (!File::exists($chunkFile)) {
                fclose($outFile);
                return response()->json(['success' => false, 'message' => "Missing chunk {$i}"], 400);
            }
            
            $inFile = fopen($chunkFile, 'rb');
            if ($inFile) {
                while (!feof($inFile)) {
                    fwrite($outFile, fread($inFile, 8192));
                }
                fclose($inFile);
                
                // Delete chunk file
                File::delete($chunkFile);
            }
        }
        
        fclose($outFile);
        
        // Clean up temp directory
        Storage::disk('public')->deleteDirectory($tempDir);
        
        // Get file size
        $fileSize = filesize($fullPath);
        
        // Handle poster upload
        $posterPath = null;
        if ($request->hasFile('poster')) {
            $posterPath = $request->file('poster')->store('videos/posters', 'public');
        }
        
        // Handle thumbnail upload
        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('videos/thumbnails', 'public');
        }
        
        // Determine HLS quality
        $convertToHls = $request->boolean('convert_to_hls', true);
        $hlsQuality = $request->input('hls_quality', '480p');
        
        // Create video record
        $video = Video::create([
            'category_id' => $request->category_id,
            'title' => $request->title,
            'description' => $request->description,
            'poster' => $posterPath,
            'thumbnail' => $thumbnailPath,
            'video_path' => $finalPath,
            'file_size' => $fileSize,
            'is_external_url' => false,
            'processing_status' => $convertToHls ? 'pending' : 'completed',
            'processing_progress' => $convertToHls ? 0 : 100,
            'hls_conversion_status' => $convertToHls ? 'pending' : 'completed',
            'status' => $request->boolean('status', true),
        ]);
        
        // Queue the video processing job if HLS conversion is selected
        if ($convertToHls && $finalPath) {
            ProcessVideoJob::dispatch($video, $hlsQuality);
            
            $qualityMsg = $hlsQuality === 'adaptive' 
                ? 'Adaptive (Multiple Qualities)' 
                : ucfirst($hlsQuality);
            
            return response()->json([
                'success' => true,
                'video_id' => $video->id,
                'processing_started' => true,
                'message' => "Video uploaded! Converting to HLS ({$qualityMsg}).",
                'redirect' => route('admin.videos.index')
            ]);
        }

        return response()->json([
            'success' => true,
            'video_id' => $video->id,
            'processing_started' => false,
            'message' => 'Video uploaded successfully.',
            'redirect' => route('admin.videos.index')
        ]);
    }

    private function deleteGeneratedStreams(Video $video): void
    {
        if ($video->hls_directory) {
            Storage::disk('public')->deleteDirectory($video->hls_directory);
        }

        if ($video->hls_playlist_path) {
            Storage::disk('public')->delete($video->hls_playlist_path);
        }
    }

    private function shouldReturnJson(Request $request): bool
    {
        return $request->expectsJson() || $request->ajax() || $request->wantsJson();
    }
}
