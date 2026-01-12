<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    protected $fillable = [
        'name',
        'description',
        'address',
        'longitude',
        'latitude',
        'phone',
        'is_active',
        'qr_code'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
