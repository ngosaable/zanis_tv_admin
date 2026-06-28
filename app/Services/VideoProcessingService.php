<?php

namespace App\Services;

use App\Models\Video;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VideoProcessingService
{
    /**
     * Available quality presets for HLS conversion
     */
    public static function getQualityPresets(): array
    {
        return [
            '1080p' => [
                'name' => '1080p Full HD',
                'width' => 1920,
                'height' => 1080,
                'bitrate' => '5000k',
                'maxrate' => '5400k',
                'bufsize' => '9000k',
            ],
            '720p' => [
                'name' => '720p HD',
                'width' => 1280,
                'height' => 720,
                'bitrate' => '2500k',
                'maxrate' => '2700k',
                'bufsize' => '4500k',
            ],
            '480p' => [
                'name' => '480p SD',
                'width' => 854,
                'height' => 480,
                'bitrate' => '1000k',
                'maxrate' => '1100k',
                'bufsize' => '1800k',
            ],
            '360p' => [
                'name' => '360p Low',
                'width' => 640,
                'height' => 360,
                'bitrate' => '600k',
                'maxrate' => '660k',
                'bufsize' => '1080k',
            ],
        ];
    }

    /**
     * Get video metadata using FFmpeg
     */
    public function getVideoMetadata(Video $video): array
    {
        if (!$video->video_path) {
            throw new \Exception('No video file found');
        }

        $videoPath = Storage::disk('public')->path($video->video_path);
        
        if (!file_exists($videoPath)) {
            throw new \Exception('Video file does not exist: ' . $videoPath);
        }

        // Get video info using FFmpeg
        $command = sprintf(
            'ffprobe -v quiet -print_format json -show_format -show_streams %s',
            escapeshellarg($videoPath)
        );

        $output = shell_exec($command);
        $data = json_decode($output, true);

        if (!$data) {
            throw new \Exception('Failed to get video metadata');
        }

        $videoStream = null;
        foreach ($data['streams'] as $stream) {
            if ($stream['codec_type'] === 'video') {
                $videoStream = $stream;
                break;
            }
        }

        if (!$videoStream) {
            throw new \Exception('No video stream found');
        }

        return [
            'duration' => (int) round($data['format']['duration'] ?? 0),
            'resolution' => sprintf('%dx%d', $videoStream['width'] ?? 0, $videoStream['height'] ?? 0),
            'file_size' => $data['format']['size'] ?? 0,
            'bitrate' => $data['format']['bit_rate'] ?? 0,
            'fps' => eval('return ' . ($videoStream['r_frame_rate'] ?? '0/1') . ';'),
        ];
    }

    /**
     * Convert video to HLS with specified quality
     * @param Video $video
     * @param string $quality - Quality preset: 1080p, 720p, 480p, 360p
     * @param callable|null $onProgress - Callback for progress updates (receives percent 0-100)
     * @return array
     */
    public function convertToHls(Video $video, string $quality = '720p', callable $onProgress = null): array
    {
        if (!$video->video_path) {
            throw new \Exception('No video file found');
        }

        $videoPath = Storage::disk('public')->path($video->video_path);
        
        if (!file_exists($videoPath)) {
            throw new \Exception('Video file does not exist: ' . $videoPath);
        }

        $presets = self::getQualityPresets();
        $preset = $presets[$quality] ?? $presets['720p'];
        
        // Create unique directory for HLS files
        $hlsDirectory = 'videos/hls/' . Str::uuid();
        $playlistPath = $hlsDirectory . '/playlist.m3u8';
        
        // Ensure directory exists
        $fullHlsDirectory = Storage::disk('public')->path($hlsDirectory);
        if (!is_dir($fullHlsDirectory)) {
            mkdir($fullHlsDirectory, 0755, true);
        }
        
        // Paths
        $outputPath = Storage::disk('public')->path($playlistPath);
        
        // Build FFmpeg command with progress output to stderr
        $cmd = sprintf(
            "ffmpeg -progress pipe:1 -i \"%s\" -c:v libx264 -c:a aac -movflags +faststart -vf scale=%d:%d -b:v %s -maxrate %s -bufsize %s -hls_time 10 -hls_playlist_type vod -hls_segment_filename \"%s\\segment_%%03d.ts\" -y \"%s\" 2>&1",
            $videoPath,
            $preset['width'],
            $preset['height'],
            $preset['bitrate'],
            $preset['maxrate'],
            $preset['bufsize'],
            $fullHlsDirectory,
            $outputPath
        );
        
        \Log::info('Running FFmpeg command for progress tracking...');
        
        // Execute using proc_open to capture progress
        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];
        
        $process = proc_open($cmd, $descriptors, $pipes);
        
        if (!is_resource($process)) {
            throw new \Exception('Failed to start FFmpeg process');
        }
        
        // Close input pipe
        fclose($pipes[0]);
        
        // Set streams to non-blocking
        stream_set_blocking($pipes[1], false);
        stream_set_blocking($pipes[2], false);
        
        $duration = $video->duration ?? 0;
        $lastProgress = 20;
        $output = '';
        
        while (true) {
            $status = proc_get_status($process);
            
            // Read stdout for progress
            while ($line = fgets($pipes[1])) {
                $output .= $line;
                // Parse progress from FFmpeg progress output - raw percentage (0-100)
                if ($duration > 0 && preg_match('/out_time_ms=(\d+)/', $line, $matches)) {
                    $currentTimeMs = (int)$matches[1];
                    $currentTime = $currentTimeMs / 1000000; // Convert to seconds
                    // Raw percentage for this conversion (0-100)
                    $conversionPercent = min(100, ($currentTime / $duration) * 100);
                    $conversionPercent = (int)$conversionPercent;
                    
                    if ($conversionPercent > $lastProgress && $onProgress) {
                        $onProgress($conversionPercent);
                        $lastProgress = $conversionPercent;
                    }
                }
            }
            
            // Also read stderr
            while ($line = fgets($pipes[2])) {
                $output .= $line;
            }
            
            if (!$status['running']) {
                break;
            }
            
            usleep(100000); // 100ms
        }
        
        fclose($pipes[1]);
        fclose($pipes[2]);
        $returnCode = proc_close($process);
        
        \Log::info('FFmpeg output: ' . substr($output, 0, 2000));
        \Log::info('FFmpeg return code: ' . $returnCode);
        
        // Check if playlist was created
        if (file_exists($outputPath)) {
            if ($onProgress) $onProgress(98);
            
            return [
                'success' => true,
                'playlist_path' => $playlistPath,
                'directory' => $hlsDirectory,
                'quality' => $quality,
                'output' => substr($output, 0, 2000),
            ];
        }
        
        // Log error
        $errorMsg = 'FFmpeg failed. Return code: ' . $returnCode . '. Output: ' . substr($output, 0, 2000);
        \Log::error($errorMsg);
        
        return [
            'success' => false,
            'error' => $errorMsg,
            'output' => substr($output, 0, 2000),
        ];
    }



    /**
     * Convert video to HLS with MULTIPLE qualities (adaptive streaming)
     * @param Video $video
     * @param array $qualities - Array of quality keys: ['1080p', '720p', '480p']
     * @param callable|null $onProgress - Callback for progress updates (receives percent 0-100)
     * @return array
     */
    public function convertToAdaptiveHls(Video $video, array $qualities = ['720p', '480p', '360p'], callable $onProgress = null): array
    {
        if (!$video->video_path) {
            throw new \Exception('No video file found');
        }

        $videoPath = Storage::disk('public')->path($video->video_path);
        
        if (!file_exists($videoPath)) {
            throw new \Exception('Video file does not exist: ' . $videoPath);
        }

        $presets = self::getQualityPresets();
        
        // Create unique directory for HLS files
        $hlsDirectory = 'videos/hls/' . Str::uuid();
        $fullHlsDirectory = Storage::disk('public')->path($hlsDirectory);
        
        if (!is_dir($fullHlsDirectory)) {
            mkdir($fullHlsDirectory, 0755, true);
        }

        $generatedQualities = [];
        $totalQualities = count($qualities);
        
        foreach ($qualities as $index => $quality) {
            $preset = $presets[$quality] ?? null;
            if (!$preset) continue;

            // Calculate progress offset for this quality
            $progressOffset = 20 + ($index / $totalQualities) * 70;
            $progressRange = 70 / $totalQualities;
            
            // Create a callback for this quality conversion
            $qualityCallback = null;
            if ($onProgress) {
                $qualityCallback = function($percent) use ($onProgress, $progressOffset, $progressRange) {
                    $overallPercent = (int)($progressOffset + ($percent / 100) * $progressRange);
                    $onProgress(min(95, $overallPercent));
                };
            }
            
            // Convert this quality
            $result = $this->convertToHls($video, $quality, $qualityCallback);
            
            if ($result['success']) {
                $generatedQualities[] = [
                    'quality' => $quality,
                    'name' => $preset['name'],
                    'resolution' => "{$preset['width']}x{$preset['height']}",
                    'bitrate' => $preset['bitrate'],
                    'playlist_path' => $result['playlist_path'],
                ];
            }
        }

        // Create master playlist for adaptive streaming
        $masterPlaylistPath = $hlsDirectory . '/playlist.m3u8';
        $fullMasterPlaylistPath = Storage::disk('public')->path($masterPlaylistPath);
        
        $masterContent = "#EXTM3U\n#EXT-X-VERSION:3\n";
        
        foreach ($generatedQualities as $q) {
            $bandwidth = (int) str_replace('k', '000', $q['bitrate']);
            $masterContent .= sprintf(
                "#EXT-X-STREAM-INF:BANDWIDTH=%d,RESOLUTION=%s\n%s\n",
                $bandwidth,
                $q['resolution'],
                $q['playlist_path']
            );
        }
        
        file_put_contents($fullMasterPlaylistPath, $masterContent);

        if ($onProgress) $onProgress(98);
        
        return [
            'success' => true,
            'playlist_path' => $masterPlaylistPath,
            'directory' => $hlsDirectory,
            'qualities' => $generatedQualities,
        ];
    }

    /**
     * Extract thumbnail from video
     */
    public function extractThumbnail(Video $video, $time = '00:00:10'): ?string
    {
        if (!$video->video_path) {
            return null;
        }

        $videoPath = Storage::disk('public')->path($video->video_path);
        
        if (!file_exists($videoPath)) {
            return null;
        }

        $thumbnailPath = 'videos/thumbnails/' . Str::uuid() . '.jpg';
        $fullThumbnailPath = Storage::disk('public')->path($thumbnailPath);

        // Ensure directory exists
        $thumbnailDir = dirname($fullThumbnailPath);
        if (!is_dir($thumbnailDir)) {
            mkdir($thumbnailDir, 0755, true);
        }

        $command = sprintf(
            'ffmpeg -i %s -ss %s -vframes 1 -y %s 2>&1',
            escapeshellarg($videoPath),
            escapeshellarg($time),
            escapeshellarg($fullThumbnailPath)
        );

        shell_exec($command);

        if (file_exists($fullThumbnailPath)) {
            return $thumbnailPath;
        }

        return null;
    }

    /**
     * Check if FFmpeg is available
     */
    public function checkFFmpeg(): bool
    {
        $version = shell_exec('ffmpeg -version 2>&1');
        return strpos($version, 'ffmpeg') !== false;
    }

    /**
     * Check if FFprobe is available
     */
    public function checkFFprobe(): bool
    {
        $version = shell_exec('ffprobe -version 2>&1');
        return strpos($version, 'ffprobe') !== false;
    }
}
