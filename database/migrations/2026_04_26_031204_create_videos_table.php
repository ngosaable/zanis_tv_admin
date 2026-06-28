<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            
            // Basic info
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            
            // Images
            $table->string('poster')->nullable();
            $table->string('thumbnail')->nullable();
            
            // Video files
            $table->string('video_path')->nullable(); // original uploaded video
            $table->string('hls_playlist_path')->nullable(); // path to .m3u8 file
            $table->string('hls_directory')->nullable(); // directory containing HLS segments
            
            // Video metadata
            $table->integer('duration')->nullable(); // in seconds
            $table->string('resolution')->nullable(); // e.g. '1920x1080'
            $table->bigInteger('file_size')->nullable(); // in bytes
            
            // Processing status
            $table->string('processing_status')->default('pending'); // pending|processing|completed|failed
            $table->string('hls_conversion_status')->default('pending'); // pending|processing|completed|failed
            
            // Status
            $table->boolean('status')->default(true);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
