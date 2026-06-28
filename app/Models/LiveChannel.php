<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveChannel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category_id',
        'stream_url',
        'logo',
        'description',
        'is_live',
        'status',
    ];

    protected $casts = [
        'is_live' => 'boolean',
        'status' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
