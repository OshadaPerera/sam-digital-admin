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
        Schema::create('slider_slides', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['image', 'video'])->default('image');
            $table->string('media_path');
            $table->text('alt_text')->nullable();
            $table->enum('screen_size', ['small', 'large'])->default('large');
            $table->integer('order')->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            // Add indexes for performance
            $table->index('screen_size');
            $table->index('status');
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slider_slides');
    }
};
