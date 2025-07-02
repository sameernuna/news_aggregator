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
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('preference_type', ['category', 'source', 'author']);
            $table->unsignedBigInteger('preference_id');
            $table->timestamps();
            
            // Unique constraint to prevent duplicate preferences
            $table->unique(['user_id', 'preference_type', 'preference_id']);
            
            // Indexes for better performance
            $table->index('user_id');
            $table->index('preference_type');
            $table->index('preference_id');
            
            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};
