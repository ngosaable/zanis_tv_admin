<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WatchHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'video_id',
        'watch_time', // seconds watched
        'total_duration', // total video duration in seconds
        'watched_at',
        'completed', // whether user finished watching
        'device_info', // device type, browser, etc.
        'ip_address',
    ];

    protected $casts = [
        'watch_time' => 'integer',
        'total_duration' => 'integer',
        'watched_at' => 'datetime',
        'completed' => 'boolean',
        'device_info' => 'json',
    ];

    /**
     * RELATIONSHIPS
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function video()
    {
        return $this->belongsTo(Video::class);
    }

    /**
     * SCOPES
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeCompleted($query)
    {
        return $query->where('completed', true);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('watched_at', '>=', now()->subDays($days));
    }

    /**
     * METHODS
     */
    public function getWatchPercentageAttribute(): float
    {
        if ($this->total_duration == 0) {
            return 0;
        }

        return ($this->watch_time / $this->total_duration) * 100;
    }

    public function isCompleted(): bool
    {
        return $this->completed || $this->watch_percentage >= 90;
    }

    public function markAsCompleted(): void
    {
        $this->completed = true;
        $this->watch_time = $this->total_duration;
        $this->save();
    }

    public function updateWatchTime(int $seconds): void
    {
        $this->watch_time = min($seconds, $this->total_duration);
        
        if ($this->watch_percentage >= 90) {
            $this->completed = true;
        }
        
        $this->save();
    }

    /**
     * STATIC METHODS
     */
    public static function getContinueWatching(User $user, int $limit = 10)
    {
        return self::byUser($user->id)
            ->with('video')
            ->where('completed', false)
            ->orderBy('watched_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public static function getWatchedVideos(User $user, int $limit = 20)
    {
        return self::byUser($user->id)
            ->with('video')
            ->orderBy('watched_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public static function getTopWatchedVideos(int $limit = 10)
    {
        return self::select('video_id')
            ->selectRaw('COUNT(*) as watch_count, AVG(watch_time) as avg_watch_time')
            ->with('video')
            ->groupBy('video_id')
            ->orderBy('watch_count', 'desc')
            ->limit($limit)
            ->get();
    }
}
