<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'video_id',
        'rating', // 1-5 stars
        'review',
        'ip_address',
    ];

    protected $casts = [
        'rating' => 'decimal:1',
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

    public function scopeByVideo($query, $videoId)
    {
        return $query->where('video_id', $videoId);
    }

    public function scopeWithReview($query)
    {
        return $query->whereNotNull('review')->where('review', '!=', '');
    }

    /**
     * METHODS
     */
    public function getStarsAttribute(): string
    {
        $stars = '';
        $rating = (int) $this->rating;
        
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $rating) {
                $stars .= '★';
            } else {
                $stars .= '☆';
            }
        }
        
        return $stars;
    }

    public function hasReview(): bool
    {
        return !empty($this->review);
    }

    /**
     * STATIC METHODS
     */
    public static function getAverageRating(Video $video): float
    {
        return self::byVideo($video->id)->avg('rating') ?? 0;
    }

    public static function getTotalRatings(Video $video): int
    {
        return self::byVideo($video->id)->count();
    }

    public static function getRatingDistribution(Video $video): array
    {
        $distribution = self::byVideo($video->id)
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        return [
            5 => $distribution[5] ?? 0,
            4 => $distribution[4] ?? 0,
            3 => $distribution[3] ?? 0,
            2 => $distribution[2] ?? 0,
            1 => $distribution[1] ?? 0,
        ];
    }

    public static function getUserRating(User $user, Video $video): ?self
    {
        return self::byUser($user->id)->byVideo($video->id)->first();
    }

    public static function rateVideo(User $user, Video $video, float $rating, string $review = null): self
    {
        return self::updateOrCreate(
            ['user_id' => $user->id, 'video_id' => $video->id],
            [
                'rating' => max(1, min(5, $rating)),
                'review' => $review,
                'ip_address' => request()->ip(),
            ]
        );
    }

    public static function getTopRatedVideos(int $limit = 10)
    {
        return Video::select('videos.*')
            ->selectRaw('AVG(video_ratings.rating) as avg_rating, COUNT(video_ratings.id) as rating_count')
            ->join('video_ratings', 'videos.id', '=', 'video_ratings.video_id')
            ->where('videos.status', true)
            ->groupBy('videos.id')
            ->having('rating_count', '>=', 5)
            ->orderBy('avg_rating', 'desc')
            ->limit($limit)
            ->get();
    }
}
