<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'icon'
    ];

    public function restaurants()
    {
        return $this->belongsToMany(Restaurant::class, 'restaurant_category');
    }

    public function translations()
    {
        return $this->hasMany(CategoryTranslation::class);
    }

    /**
     * Get translated name based on current locale
     */
    public function getTranslatedName($locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        // Use loaded translations to avoid N+1 query
        $translation = $this->translations->firstWhere('code', $locale);

        return $translation ? $translation->name : null;
    }
}
