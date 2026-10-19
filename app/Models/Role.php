<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
    ];

    /**
     * Users are linked by the role *name* stored in users.role
     */
    public function users()
    {
        return $this->hasMany(User::class, 'role', 'name');
    }
}