<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_request_id',
        'user_id',
        'invoice_number',
        'issue_date',
        'due_date',
        'subtotal',
        'discount_amount',
        'penalty_amount',
        'total_amount',
        'status',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'due_date' => 'date',
            'subtotal' => 'integer',
            'discount_amount' => 'integer',
            'penalty_amount' => 'integer',
            'total_amount' => 'integer',
            'paid_at' => 'datetime',
        ];
    }

    public function bookingRequest(): BelongsTo
    {
        return $this->belongsTo(BookingRequest::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function paymentAttempts(): HasMany
    {
        return $this->hasMany(PaymentAttempt::class);
    }

    public function latestPaymentAttempt(): HasOne
    {
        return $this->hasOne(PaymentAttempt::class)->latestOfMany();
    }

    public function paymentEvents(): HasMany
    {
        return $this->hasMany(PaymentEvent::class);
    }

    public function lease(): HasOne
    {
        return $this->hasOne(Lease::class);
    }

    public function latestPaymentAttemptRecord(): ?PaymentAttempt
    {
        if (! $this->relationLoaded('latestPaymentAttempt')) {
            $this->load('latestPaymentAttempt');
        }

        /** @var ?PaymentAttempt $attempt */
        $attempt = $this->getRelation('latestPaymentAttempt');

        return $attempt;
    }

    public function canContinuePayment(): bool
    {
        if ($this->status === 'paid') {
            return false;
        }

        return $this->latestPaymentAttemptRecord()?->canContinueCheckout() ?? false;
    }

    public function canCreatePaymentAttempt(): bool
    {
        if ($this->status === 'paid') {
            return false;
        }

        $latestAttempt = $this->latestPaymentAttemptRecord();

        if (! $latestAttempt instanceof PaymentAttempt) {
            return true;
        }

        return $latestAttempt->isRetryable();
    }
}
