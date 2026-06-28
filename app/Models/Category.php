<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    /**
     * Type casting
     */
    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function videos()
    {
        return $this->hasMany(Video::class);
    }

    /**
     * Scope to get only active categories
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Get count of videos in this category
     */
    public function getVideosCountAttribute(): int
    {
        return $this->videos()->where('status', true)->count();
    }
}
