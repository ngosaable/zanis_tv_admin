<?php

namespace App\Jobs;

use App\Models\Video;
use App\Services\VideoProcessingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ProcessVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600; // 60 minutes for larger videos
    public $tries = 3;

    protected $video;
    protected $quality;

    public function __construct(Video $video, string $quality = '720p')
    {
        $this->video = $video;
        $this->quality = $quality;
    }

    public function handle(VideoProcessingService $processingService): void
    {
        try {
            // Mark as processing
            $this->video->update([
                'processing_status' => 'processing',
                'hls_conversion_status' => 'processing',
                'processing_progress' => 5,
            ]);

            // Get video metadata
            $metadata = $processingService->getVideoMetadata($this->video);
            
            // Update video with metadata
            $this->video->update([
                'duration' => $metadata['duration'] ?? null,
                'resolution' => $metadata['resolution'] ?? null,
                'file_size' => $metadata['file_size'] ?? $this->video->file_size,
                'processing_progress' => 15,
            ]);

            // Progress callback for HLS conversion
            $video = $this->video; // capture for closure
            $onProgress = function($percent) use ($video) {
                // $percent is 0-100 for the conversion step
                // Map to overall progress: metadata (0-15), conversion (15-95), completion (95-100)
                $overall = 15 + ($percent / 100) * 80; // 15-95
                $video->update(['processing_progress' => min(95, (int)$overall)]);
            };

            // Convert to HLS based on quality setting
            if ($this->quality === 'adaptive') {
                $this->video->update(['processing_progress' => 20]);
                $hlsResult = $processingService->convertToAdaptiveHls($this->video, ['720p', '480p', '360p'], $onProgress);
            } else {
                // Single quality conversion
                $hlsResult = $processingService->convertToHls($this->video, $this->quality, $onProgress);
            }

            if ($hlsResult['success']) {
                $this->video->update([
                    'hls_playlist_path' => $hlsResult['playlist_path'],
                    'hls_directory' => $hlsResult['directory'],
                    'hls_qualities' => $hlsResult['qualities'] ?? null,
                    'processing_status' => 'completed',
                    'hls_conversion_status' => 'completed',
                    'processing_progress' => 100,
                ]);
            } else {
                throw new \Exception('HLS conversion failed: ' . ($hlsResult['error'] ?? 'Unknown error'));
            }

        } catch (\Exception $e) {
            $this->video->update([
                'processing_status' => 'failed',
                'hls_conversion_status' => 'failed',
            ]);

            \Log::error('Video processing failed', [
                'video_id' => $this->video->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            if ($this->attempts() < $this->tries) {
                $this->release(300);
            }
        }
    }

    public function failed(\Throwable $exception): void
    {
        $this->video->update([
            'processing_status' => 'failed',
            'hls_conversion_status' => 'failed',
        ]);

        \Log::error('Video processing job failed permanently', [
            'video_id' => $this->video->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
