<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PlotImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'plot_id',
        'image_path',
        'is_primary',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function plot(): BelongsTo
    {
        return $this->belongsTo(Plot::class);
    }

    /**
     * Resolve image_path to a displayable URL.
     * Supports external URLs and local storage paths.
     */
    public function getUrlAttribute(): ?string
    {
        $path = $this->image_path;

        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, '/storage/')) {
            return $path;
        }

        $normalizedPath = ltrim($path, '/');

        if (str_starts_with($normalizedPath, 'storage/')) {
            $normalizedPath = substr($normalizedPath, strlen('storage/'));
        }

        $publicDisk = Storage::disk('public');
        if ($publicDisk->exists($normalizedPath)) {
            return $publicDisk->url($normalizedPath);
        }

        if (! Storage::exists($normalizedPath)) {
            return null;
        }

        return Storage::url($normalizedPath);
    }
}
