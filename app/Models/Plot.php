<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plot extends Model
{
    use HasFactory;

    protected $fillable = [
        'market_id',
        'area_id',
        'name',
        'type',
        'length',
        'width',
        'area_square_meters',
        'floor_level',
        'location_note',
        'base_price_monthly',
        'base_price_yearly',
        'status',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'length' => 'decimal:2',
            'width' => 'decimal:2',
            'area_square_meters' => 'decimal:2',
            'base_price_monthly' => 'integer',
            'base_price_yearly' => 'integer',
        ];
    }

    public function market(): BelongsTo
    {
        return $this->belongsTo(Market::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(PlotImage::class);
    }

    public function bookingRequests(): HasMany
    {
        return $this->hasMany(BookingRequest::class);
    }

    public function leases(): HasMany
    {
        return $this->hasMany(Lease::class);
    }
}
