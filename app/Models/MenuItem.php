<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = [
        'menu_section_id',
        'image_path',
        'base_price',
        'weight',
    ];

    public function menuSection()
    {
        return $this->belongsTo(MenuSection::class);
    }

    public function restaurantMenuItems()
    {
        return $this->hasMany(RestaurantMenuItem::class);
    }

    public function translations()
    {
        return $this->hasMany(MenuItemTranslation::class);
    }

    public function getTranslation(?string $langCode = null)
    {
        $langCode = $langCode ?? \App\Helpers\LanguageHelper::getCurrentLang();
        return $this->translations()->where('lang_code', $langCode)->first();
    }

    public function getNameAttribute()
    {
        $translation = $this->getTranslation();
        return $translation ? $translation->name : null;
    }

    public function getDescriptionAttribute()
    {
        $translation = $this->getTranslation();
        return $translation ? $translation->description : null;
    }
}
