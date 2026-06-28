<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            // path stored like: ads/banner.jpg
            if (!Schema::hasColumn('ads', 'image')) {
                $table->string('image')->nullable()->after('title');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            if (Schema::hasColumn('ads', 'image')) {
                $table->dropColumn('image');
            }
        });
    }
};
