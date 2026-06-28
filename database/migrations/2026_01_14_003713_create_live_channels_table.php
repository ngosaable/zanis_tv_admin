<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('live_channels', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();

            // HLS URL e.g. https://domain.com/live/stream.m3u8
            $table->string('stream_url');

            // optional logo image path in storage
            $table->string('logo')->nullable();

            $table->boolean('is_live')->default(true);
            $table->boolean('status')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_channels');
    }
};
