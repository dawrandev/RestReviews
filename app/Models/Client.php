<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Client extends Model
{
    use HasApiTokens;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'image_path'
    ];

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the reviews written by the client.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the client's favorite restaurants.
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Get the restaurants favorited by the client.
     */
    public function favoriteRestaurants()
    {
        return $this->belongsToMany(Restaurant::class, 'favorites');
    }
}
