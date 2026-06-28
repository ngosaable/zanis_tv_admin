<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ads', function (Blueprint $table) {
            $table->id();

            $table->string('title')->nullable();

            $table->enum('ad_type', ['banner', 'video', 'popup'])->default('banner');
            $table->enum('position', ['home', 'player', 'splash'])->default('home');

            // stored path in public disk OR remote url
            $table->string('media')->nullable();

            $table->string('target_url')->nullable();

            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();

            $table->boolean('status')->default(true);

            $table->timestamps();

            $table->index(['status', 'position', 'ad_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};
