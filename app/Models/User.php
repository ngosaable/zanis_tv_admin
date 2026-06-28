<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'avatar',
        'date_of_birth',
        'gender',
        'country',
        'language',
        'timezone',
        'preferences',
        'subscription_plan_id',
        'subscription_status',
        'subscription_ends_at',
        'is_active',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'date_of_birth' => 'date',
        'preferences' => 'json',
        'subscription_ends_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * ROLE HELPERS
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isContentManager(): bool
    {
        return $this->role === 'content_manager';
    }

    public function isSubscriber(): bool
    {
        return in_array($this->role, ['subscriber', 'premium_subscriber']);
    }

    public function isPremium(): bool
    {
        return $this->role === 'premium_subscriber';
    }

    /**
     * RELATIONSHIPS
     */
    public function subscription()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function playlists()
    {
        return $this->hasMany(Playlist::class);
    }

    public function watchHistory()
    {
        return $this->hasMany(WatchHistory::class);
    }

    public function favorites()
    {
        return $this->belongsToMany(Video::class, 'user_favorites');
    }

    public function ratings()
    {
        return $this->hasMany(VideoRating::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * SUBSCRIPTION METHODS
     */
    public function hasActiveSubscription(): bool
    {
        return $this->subscription_status === 'active' && 
               $this->subscription_ends_at && 
               $this->subscription_ends_at->isFuture();
    }

    public function canWatchPremiumContent(): bool
    {
        return $this->hasActiveSubscription() || $this->isPremium();
    }

    /**
     * PREFERENCES METHODS
     */
    public function getPreference(string $key, $default = null)
    {
        $preferences = $this->preferences ?? [];
        return $preferences[$key] ?? $default;
    }

    public function setPreference(string $key, $value): void
    {
        $preferences = $this->preferences ?? [];
        $preferences[$key] = $value;
        $this->preferences = $preferences;
        $this->save();
    }

    /**
     * AVATAR URL
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        
        // Generate default avatar with initials
        $initials = collect(explode(' ', $this->name))
            ->map(fn($word) => strtoupper(substr($word, 0, 1)))
            ->take(2)
            ->implode('');
            
        return "https://ui-avatars.com/api/?name={$initials}&color=7F9CF5&background=EBF4FF";
    }

    /**
     * WATCH HISTORY
     */
    public function addToWatchHistory(Video $video, int $watchTime = 0): void
    {
        WatchHistory::updateOrCreate(
            ['user_id' => $this->id, 'video_id' => $video->id],
            [
                'watch_time' => $watchTime,
                'total_duration' => $video->duration,
                'watched_at' => now(),
            ]
        );
    }

    /**
     * FAVORITES
     */
    public function toggleFavorite(Video $video): bool
    {
        if ($this->favorites()->where('video_id', $video->id)->exists()) {
            $this->favorites()->detach($video->id);
            return false;
        } else {
            $this->favorites()->attach($video->id);
            return true;
        }
    }

    /**
     * RECOMMENDATIONS
     */
    public function getRecommendedVideos(int $limit = 10)
    {
        // Get videos based on watch history and preferences
        $watchedCategories = $this->watchHistory()
            ->with('video.category')
            ->get()
            ->pluck('video.category.id')
            ->unique()
            ->filter();

        return Video::whereIn('category_id', $watchedCategories)
            ->whereNotIn('id', $this->watchHistory()->pluck('video_id'))
            ->where('status', true)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
