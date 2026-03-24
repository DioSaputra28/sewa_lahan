<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lease extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_request_id',
        'tenant_id',
        'plot_id',
        'invoice_id',
        'lease_number',
        'start_date',
        'end_date',
        'term_type',
        'duration',
        'agreed_price',
        'deposit_amount',
        'status',
        'activated_at',
        'renewal_of_lease_id',
    ];

    protected function casts(): array
    {
        return [
            'duration' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
            'agreed_price' => 'integer',
            'deposit_amount' => 'integer',
            'activated_at' => 'datetime',
        ];
    }

    public function bookingRequest(): BelongsTo
    {
        return $this->belongsTo(BookingRequest::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function plot(): BelongsTo
    {
        return $this->belongsTo(Plot::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function renewalOfLease(): BelongsTo
    {
        return $this->belongsTo(Lease::class, 'renewal_of_lease_id');
    }

    public function renewals(): HasMany
    {
        return $this->hasMany(Lease::class, 'renewal_of_lease_id');
    }

    public function periods(): HasMany
    {
        return $this->hasMany(LeasePeriod::class);
    }

    public function hasUnresolvedRenewalRequest(): bool
    {
        return BookingRequest::query()
            ->where('user_id', $this->tenant_id)
            ->where('plot_id', $this->plot_id)
            ->where('renewal_of_lease_id', $this->id)
            ->whereIn('status', ['pending', 'approved'])
            ->where('payment_status', '!=', 'paid')
            ->exists();
    }
}
