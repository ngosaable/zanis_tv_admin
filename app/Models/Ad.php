<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'image',        // ads/images/xxxx.jpg (storage path)
        'ad_type',
        'position',
        'target_url',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'status'     => 'boolean',
        'start_date' => 'datetime',
        'end_date'   => 'datetime',
    ];

    /**
     * Get the full URL for the image
     */
    public function getImageFullUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }
        
        return asset('storage/' . $this->image);
    }
}
