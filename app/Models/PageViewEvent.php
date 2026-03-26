<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageViewEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'visited_at',
        'route_name',
        'page_key',
        'path',
        'plot_id',
        'session_id',
        'visitor_hash',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'visited_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function plot(): BelongsTo
    {
        return $this->belongsTo(Plot::class);
    }
}
