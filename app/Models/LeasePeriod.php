<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeasePeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'lease_id',
        'period_no',
        'period_start',
        'period_end',
        'due_date',
        'amount',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'period_no' => 'integer',
            'period_start' => 'date',
            'period_end' => 'date',
            'due_date' => 'date',
            'amount' => 'integer',
        ];
    }

    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }
}
