<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class PaymentAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'user_id',
        'provider',
        'provider_project_slug',
        'provider_order_id',
        'payment_method',
        'request_amount',
        'fee',
        'total_payment',
        'payment_number',
        'checkout_url',
        'redirect_url',
        'qris_only',
        'is_sandbox',
        'status',
        'expired_at',
        'requested_at',
        'last_error_message',
    ];

    protected function casts(): array
    {
        return [
            'request_amount' => 'integer',
            'fee' => 'integer',
            'total_payment' => 'integer',
            'qris_only' => 'boolean',
            'is_sandbox' => 'boolean',
            'expired_at' => 'datetime',
            'requested_at' => 'datetime',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(PaymentEvent::class);
    }

    public function isExpired(): bool
    {
        if ($this->status === 'expired') {
            return true;
        }

        if (! $this->expired_at instanceof Carbon) {
            return false;
        }

        return $this->expired_at->isPast();
    }

    public function isRetryable(): bool
    {
        if ($this->isExpired()) {
            return true;
        }

        return in_array($this->status, ['failed', 'cancelled'], true);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function canContinueCheckout(): bool
    {
        return filled($this->checkout_url)
            && (! $this->isRetryable())
            && (! $this->isCompleted());
    }
}
