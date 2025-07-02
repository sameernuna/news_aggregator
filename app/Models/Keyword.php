<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Keyword extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Get the articles that belong to this keyword.
     */
    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(NewsArticle::class, 'article_keyword', 'keyword_id', 'article_id');
    }
}
