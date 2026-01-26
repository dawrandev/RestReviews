<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [];

    public function restaurants()
    {
        return $this->hasMany(Restaurant::class);
    }

    public function translations()
    {
        return $this->hasMany(CityTranslation::class);
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
