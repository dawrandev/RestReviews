<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = [
        'name',
        'logo',
        'description'
    ];

    public function restaurants()
    {
        return $this->hasMany(Restaurant::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function menuSections()
    {
        return $this->hasMany(MenuSection::class);
    }
}
