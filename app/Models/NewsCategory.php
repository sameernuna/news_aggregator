<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NewsCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the news articles for this category.
     */
    public function articles(): HasMany
    {
        return $this->hasMany(NewsArticle::class, 'category_id');
    }

    /**
     * Get the articles count for this category.
     */
    public function getArticlesCountAttribute(): int
    {
        return $this->articles()->count();
    }
} 