<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuSection extends Model
{
    protected $fillable = [
        'brand_id',
        'name',
        'sort_order',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }
}
