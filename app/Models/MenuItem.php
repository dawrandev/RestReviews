<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = [
        'menu_section_id',
        'name',
        'description',
        'image_path',
        'base_price',
    ];

    public function menuSection()
    {
        return $this->belongsTo(MenuSection::class);
    }

    public function restaurantMenuItems()
    {
        return $this->hasMany(RestaurantMenuItem::class);
    }
}
