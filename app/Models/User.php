<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_no',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's preferences.
     */
    public function preferences(): HasMany
    {
        return $this->hasMany(UserPreference::class);
    }

    /**
     * Get the user's category preferences.
     */
    public function categoryPreferences(): HasMany
    {
        return $this->hasMany(UserPreference::class)->where('preference_type', 'category');
    }

    /**
     * Get the user's source preferences.
     */
    public function sourcePreferences(): HasMany
    {
        return $this->hasMany(UserPreference::class)->where('preference_type', 'source');
    }

    /**
     * Get the user's author preferences.
     */
    public function authorPreferences(): HasMany
    {
        return $this->hasMany(UserPreference::class)->where('preference_type', 'author');
    }

    /**
     * Get the user's preferred news.
     */
    public function preferredNews(): HasMany
    {
        return $this->hasMany(UserPreferredNews::class);
    }

    /**
     * Get the user's unread preferred news.
     */
    public function unreadPreferredNews(): HasMany
    {
        return $this->hasMany(UserPreferredNews::class)->unread();
    }
}
