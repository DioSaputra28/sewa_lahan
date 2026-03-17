<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Market extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'city',
        'maps_url',
        'status',
        'description',
    ];

    public function areas(): HasMany
    {
        return $this->hasMany(Area::class);
    }

    public function plots(): HasMany
    {
        return $this->hasMany(Plot::class);
    }
}
