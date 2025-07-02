<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Author extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'email',
        'profile_image_url',
    ];

    /**
     * Get the articles for this author.
     */
    public function articles(): HasMany
    {
        return $this->hasMany(NewsArticle::class, 'author_id');
    }
} 