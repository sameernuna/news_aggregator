<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreference extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'preference_type',
        'preference_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'preference_id' => 'integer',
    ];

    /**
     * Get the user that owns the preference.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category preference.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(NewsCategory::class, 'preference_id');
    }

    /**
     * Get the publisher preference.
     */
    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class, 'preference_id');
    }

    /**
     * Get the author preference.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class, 'preference_id');
    }

    /**
     * Get the preferred item based on type.
     */
    public function getPreferredItemAttribute()
    {
        switch ($this->preference_type) {
            case 'category':
                return $this->belongsTo(NewsCategory::class, 'preference_id')->first();
            case 'source':
                return $this->belongsTo(Publisher::class, 'preference_id')->first();
            case 'author':
                return $this->belongsTo(Author::class, 'preference_id')->first();
            default:
                return null;
        }
    }

    /**
     * Scope to get category preferences.
     */
    public function scopeCategories($query)
    {
        return $query->where('preference_type', 'category');
    }

    /**
     * Scope to get source preferences.
     */
    public function scopeSources($query)
    {
        return $query->where('preference_type', 'source');
    }

    /**
     * Scope to get author preferences.
     */
    public function scopeAuthors($query)
    {
        return $query->where('preference_type', 'author');
    }
}
