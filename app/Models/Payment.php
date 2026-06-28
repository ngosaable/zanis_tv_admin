<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'amount',
        'currency',
        'payment_method', // stripe, paypal, bank_transfer, mobile_money
        'transaction_id',
        'status', // pending, completed, failed, refunded
        'payment_gateway_response',
        'paid_at',
        'refunded_at',
        'refund_reason',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_gateway_response' => 'json',
        'metadata' => 'json',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    /**
     * RELATIONSHIPS
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    /**
     * SCOPES
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }

    /**
     * METHODS
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }

    public function getFormattedAmountAttribute(): string
    {
        $symbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'NGN' => '₦',
            'ZAR' => 'R',
        ];

        $symbol = $symbols[$this->currency] ?? $this->currency;
        return $symbol . number_format($this->amount, 2);
    }

    public function markAsCompleted(): void
    {
        $this->status = 'completed';
        $this->paid_at = now();
        $this->save();
    }

    public function markAsFailed(string $reason = null): void
    {
        $this->status = 'failed';
        if ($reason) {
            $this->metadata = array_merge($this->metadata ?? [], ['failure_reason' => $reason]);
        }
        $this->save();
    }

    public function refund(string $reason = null): void
    {
        $this->status = 'refunded';
        $this->refunded_at = now();
        $this->refund_reason = $reason;
        $this->save();
    }

    /**
     * STATIC METHODS
     */
    public static function createPayment(User $user, SubscriptionPlan $plan, string $paymentMethod): self
    {
        return self::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'amount' => $plan->price,
            'currency' => $plan->currency,
            'payment_method' => $paymentMethod,
            'status' => 'pending',
        ]);
    }
}
