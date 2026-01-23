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
}
