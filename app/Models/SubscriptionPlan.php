<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'currency',
        'billing_cycle', // monthly, yearly
        'duration_days',
        'features',
        'max_devices',
        'max_concurrent_streams',
        'video_quality', // SD, HD, 4K
        'download_allowed',
        'offline_download_limit',
        'status',
        'sort_order',
        'is_popular',
        'trial_days',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'json',
        'download_allowed' => 'boolean',
        'is_popular' => 'boolean',
        'status' => 'boolean',
        'trial_days' => 'integer',
        'max_devices' => 'integer',
        'max_concurrent_streams' => 'integer',
    ];

    /**
     * RELATIONSHIPS
     */
    public function users()
    {
        return $this->hasMany(User::class, 'subscription_plan_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * SCOPES
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    public function scopeByPrice($query, $order = 'asc')
    {
        return $query->orderBy('price', $order);
    }

    /**
     * METHODS
     */
    public function getFormattedPriceAttribute(): string
    {
        $symbol = $this->getCurrencySymbol();
        return $symbol . number_format($this->price, 2);
    }

    public function getCurrencySymbol(): string
    {
        $symbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'NGN' => '₦',
            'ZAR' => 'R',
        ];

        return $symbols[$this->currency] ?? $this->currency;
    }

    public function getBillingCycleTextAttribute(): string
    {
        return match($this->billing_cycle) {
            'monthly' => 'Monthly',
            'yearly' => 'Yearly',
            'weekly' => 'Weekly',
            default => ucfirst($this->billing_cycle),
        };
    }

    public function getVideoQualityTextAttribute(): string
    {
        return match($this->video_quality) {
            'SD' => 'Standard Definition (480p)',
            'HD' => 'High Definition (720p)',
            'FHD' => 'Full HD (1080p)',
            '4K' => 'Ultra HD (4K)',
            default => $this->video_quality,
        };
    }

    public function hasTrial(): bool
    {
        return $this->trial_days > 0;
    }

    public function getTrialEndDate(): ?\Carbon\Carbon
    {
        if (!$this->hasTrial()) {
            return null;
        }

        return now()->addDays($this->trial_days);
    }

    public function canDownload(): bool
    {
        return $this->download_allowed;
    }

    public function supportsMultipleDevices(): bool
    {
        return $this->max_devices > 1;
    }

    public function supportsConcurrentStreaming(): bool
    {
        return $this->max_concurrent_streams > 1;
    }

    /**
     * STATIC METHODS
     */
    public static function getPopularPlans()
    {
        return self::active()->popular()->orderBy('sort_order')->get();
    }

    public static function getPlansByPrice()
    {
        return self::active()->orderBy('price')->get();
    }

    public static function getPlanById(int $id)
    {
        return self::active()->findOrFail($id);
    }
}
