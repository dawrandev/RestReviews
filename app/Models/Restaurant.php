<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Restaurant extends Model
{
    protected $fillable = [
        'user_id',
        'brand_id',
        'city_id',
        'branch_name',
        'phone',
        'description',
        'address',
        'location',
        'is_active',
        'qr_code'
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function operatingHours()
    {
        return $this->hasMany(OperatingHour::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'restaurant_category');
    }

    public function images()
    {
        return $this->hasMany(RestaurantImage::class);
    }

    public function coverImage()
    {
        return $this->hasOne(RestaurantImage::class)->where('is_cover', true);
    }

    public function menuItems()
    {
        return $this->hasManyThrough(MenuItem::class, RestaurantMenuItem::class, 'restaurant_id', 'id', 'id', 'menu_item_id');
    }


    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getLatitudeAttribute()
    {
        if ($this->location) {
            $point = DB::selectOne("SELECT ST_Y(location) as lat FROM restaurants WHERE id = ?", [$this->id]);
            return $point ? $point->lat : null;
        }
        return null;
    }

    public function getLongitudeAttribute()
    {
        if ($this->location) {
            $point = DB::selectOne("SELECT ST_X(location) as lng FROM restaurants WHERE id = ?", [$this->id]);
            return $point ? $point->lng : null;
        }
        return null;
    }
}
