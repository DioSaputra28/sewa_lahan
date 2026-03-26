<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageViewDailySummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'route_name',
        'page_key',
        'plot_id',
        'total_views',
        'unique_visitors',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'total_views' => 'integer',
            'unique_visitors' => 'integer',
        ];
    }

    public function plot(): BelongsTo
    {
        return $this->belongsTo(Plot::class);
    }
}
