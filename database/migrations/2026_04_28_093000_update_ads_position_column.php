<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Change position from enum to string to allow longer values
        DB::statement("ALTER TABLE ads MODIFY COLUMN position VARCHAR(50) DEFAULT 'home_bottom'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE ads MODIFY COLUMN position ENUM('home', 'player', 'splash') DEFAULT 'home'");
    }
};