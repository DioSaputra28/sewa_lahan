<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'user_id',
        'provider',
        'provider_project_slug',
        'provider_order_id',
        'provider_status',
        'provider_payment_method',
        'provider_payment_number',
        'provider_fee',
        'provider_total_payment',
        'provider_expired_at',
        'provider_completed_at',
        'amount',
        'status',
        'paid_at',
        'failure_code',
        'failure_message',
    ];

    protected function casts(): array
    {
        return [
            'provider_fee' => 'integer',
            'provider_total_payment' => 'integer',
            'provider_expired_at' => 'datetime',
            'provider_completed_at' => 'datetime',
            'amount' => 'integer',
            'paid_at' => 'datetime',
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
}
