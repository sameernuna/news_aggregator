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
        Schema::create('news_articles', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('slug', 255)->unique();
            $table->text('content');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('source_id');
            $table->unsignedBigInteger('author_id');
            $table->dateTime('published_at')->nullable();
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('category_id')->references('id')->on('news_categories')->onDelete('cascade');
            $table->foreign('source_id')->references('id')->on('publishers')->onDelete('cascade');
            $table->foreign('author_id')->references('id')->on('authors')->onDelete('cascade');
            
            // Indexes for better performance
            $table->index('category_id');
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news_articles');
    }
};
