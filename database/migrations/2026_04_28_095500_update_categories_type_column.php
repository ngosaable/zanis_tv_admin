<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE categories MODIFY COLUMN type VARCHAR(50) DEFAULT 'movie'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE categories MODIFY COLUMN type ENUM('Movie', 'Live', 'Series', 'Documentary') DEFAULT 'Movie'");
    }
};