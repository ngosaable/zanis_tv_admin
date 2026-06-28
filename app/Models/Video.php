<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'category_id',
        'title',
        'description',

        // Images
        'poster',      // e.g. videos/posters/a.jpg
        'thumbnail',   // optional alt image

        // Video files
        'video_path',          // original uploaded video
        'hls_playlist_path',   // path to .m3u8 file
        'hls_directory',       // directory containing HLS segments
        'hls_qualities',      // JSON array of generated qualities

        // Video metadata
        'duration',            // in seconds
        'resolution',          // e.g. '1920x1080'
        'file_size',          // in bytes

        // Processing status
        'processing_status',   // pending|processing|completed|failed
        'processing_progress', // 0-100 percentage
        'processing_log',   // log messages
        'hls_conversion_status', // pending|processing|completed|failed
        'is_external_url',     // boolean - true if video_path is external URL

        // Status
        'status',
    ];

    /**
     * Type casting
     */
    protected $casts = [
        'status' => 'boolean',
        'duration' => 'integer',
        'file_size' => 'integer',
        'hls_qualities' => 'array',
        'processing_progress' => 'integer',
        'is_external_url' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Helpers for URLs
     * These create full URLs that Flutter can play immediately.
     *
     * NOTE:
     * - Requires: php artisan storage:link
     * - Your uploads are in: storage/app/public
     */

    public function getPosterFullUrlAttribute(): ?string
    {
        $path = $this->poster ?: $this->thumbnail;

        if (!$path) return null;

        // Ensure the path doesn't start with 'storage/' to avoid double storage in URL
        $cleanPath = ltrim($path, '/');
        $cleanPath = ltrim($cleanPath, 'storage/');

        return asset('storage/' . $cleanPath);
    }

    public function getThumbnailFullUrlAttribute(): ?string
    {
        if (!$this->thumbnail) return null;

        // Ensure the path doesn't start with 'storage/' to avoid double storage in URL
        $cleanPath = ltrim($this->thumbnail, '/');
        $cleanPath = ltrim($cleanPath, 'storage/');

        return asset('storage/' . $cleanPath);
    }

    public function getVideoFullUrlAttribute(): ?string
    {
        if (!$this->video_path) return null;

        if ($this->is_external_url || filter_var($this->video_path, FILTER_VALIDATE_URL)) {
            return $this->video_path;
        }
        
        // Ensure the path doesn't start with 'storage/' to avoid double storage in URL
        $cleanPath = ltrim($this->video_path, '/');
        $cleanPath = ltrim($cleanPath, 'storage/');

        return asset('storage/' . $cleanPath);
    }

    public function getHlsPlaylistFullUrlAttribute(): ?string
    {
        if (!$this->hls_playlist_path) return null;
        
        // Ensure the path doesn't start with 'storage/' to avoid double storage in URL
        $cleanPath = ltrim($this->hls_playlist_path, '/');
        $cleanPath = ltrim($cleanPath, 'storage/');

        return asset('storage/' . $cleanPath);
    }

    /**
     * Check if video is ready for streaming (HLS conversion completed)
     */
    public function isReadyForStreaming(): bool
    {
        return $this->hls_conversion_status === 'completed' && 
               $this->hls_playlist_path && 
               $this->status;
    }

    /**
     * Get formatted duration (MM:SS or HH:MM:SS)
     */
    public function getFormattedDurationAttribute(): string
    {
        if (!$this->duration) return '00:00';

        $hours = floor($this->duration / 3600);
        $minutes = floor(($this->duration % 3600) / 60);
        $seconds = $this->duration % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }

        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    /**
     * Get formatted file size (KB, MB, GB)
     */
    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) return '0 B';

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;

        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }

        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }

    /**
     * Automatically include these computed attributes
     * in JSON responses (API)
     */
    protected $appends = [
        'poster_full_url',
        'thumbnail_full_url',
        'video_full_url',
        'hls_playlist_full_url',
        'formatted_duration',
        'formatted_file_size',
    ];
}
