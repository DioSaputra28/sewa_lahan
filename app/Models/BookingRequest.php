<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BookingRequest extends Model
{
    use HasFactory;

    public const RENEWAL_MARKER_PREFIX = '[renewal_of_lease:';

    protected $fillable = [
        'user_id',
        'plot_id',
        'term_type',
        'duration',
        'start_date',
        'end_date',
        'quoted_price',
        'final_price',
        'status',
        'payment_status',
        'approved_by',
        'approved_at',
        'rejected_at',
        'rejection_reason',
        'payment_due_at',
        'expires_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'duration' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
            'quoted_price' => 'integer',
            'final_price' => 'integer',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
            'payment_due_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plot(): BelongsTo
    {
        return $this->belongsTo(Plot::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function statusEvents(): HasMany
    {
        return $this->hasMany(BookingStatusEvent::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function lease(): HasOne
    {
        return $this->hasOne(Lease::class);
    }

    public static function renewalMarkerForLease(Lease $lease): string
    {
        return static::RENEWAL_MARKER_PREFIX.$lease->id.'|'.$lease->lease_number.']';
    }

    public function renewalSourceLeaseId(): ?int
    {
        if (! filled($this->notes)) {
            return null;
        }

        preg_match('/\[renewal_of_lease:(\d+)\|[^\]]+\]/', (string) $this->notes, $matches);

        return isset($matches[1]) ? (int) $matches[1] : null;
    }
}
