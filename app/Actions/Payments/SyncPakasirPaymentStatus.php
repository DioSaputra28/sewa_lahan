<?php

namespace App\Actions\Payments;

use App\Actions\ActivityLogs\WriteOperationalActivityLog;
use App\Actions\Leases\CreateLeaseFromPaidBooking;
use App\Models\BookingRequest;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentAttempt;
use App\Models\PaymentEvent;
use App\Services\PakasirService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SyncPakasirPaymentStatus
{
    public function __construct(
        protected PakasirService $pakasirService,
        protected CreateLeaseFromPaidBooking $createLeaseFromPaidBooking,
        protected WriteOperationalActivityLog $activityLog,
    ) {}

    public function handle(Payment $payment): Payment
    {
        $detail = $this->pakasirService->getTransactionDetail($payment->invoice()->firstOrFail());

        return DB::transaction(function () use ($detail, $payment): Payment {
            $payment = Payment::query()
                ->whereKey($payment->id)
                ->lockForUpdate()
                ->firstOrFail();

            $invoice = $payment->invoice()
                ->with(['paymentAttempts'])
                ->lockForUpdate()
                ->firstOrFail();

            $bookingRequest = $invoice->bookingRequest()
                ->lockForUpdate()
                ->first();
            $transaction = $detail['transaction'] ?? [];
            $businessExpired = $this->isBusinessExpired($invoice, $bookingRequest);

            $providerStatus = (string) ($transaction['status'] ?? 'pending');
            $paymentMethod = $transaction['payment_method'] ?? $payment->provider_payment_method;
            $paymentNumber = $transaction['payment_number'] ?? $payment->provider_payment_number;
            $providerFee = isset($transaction['fee']) ? (int) $transaction['fee'] : $payment->provider_fee;
            $totalPayment = isset($transaction['total_payment']) ? (int) $transaction['total_payment'] : $payment->provider_total_payment;
            $expiredAt = isset($transaction['expired_at']) ? Carbon::parse($transaction['expired_at']) : $payment->provider_expired_at;
            $completedAt = isset($transaction['completed_at']) ? Carbon::parse($transaction['completed_at']) : null;
            $localStatus = $businessExpired ? 'expired' : $this->mapPaymentStatus($providerStatus);

            $payment->update([
                'provider_status' => $providerStatus,
                'provider_payment_method' => $paymentMethod,
                'provider_payment_number' => $paymentNumber,
                'provider_fee' => $providerFee,
                'provider_total_payment' => $totalPayment,
                'provider_expired_at' => $expiredAt,
                'provider_completed_at' => $completedAt,
                'status' => $localStatus,
                'paid_at' => $localStatus === 'paid' ? ($completedAt ?? now()) : null,
                'failure_code' => in_array($localStatus, ['failed', 'expired', 'cancelled'], true) ? strtoupper($localStatus) : null,
                'failure_message' => $this->failureMessage($localStatus),
            ]);

            $latestAttempt = $invoice->paymentAttempts()->latest('id')->first();

            if ($latestAttempt instanceof PaymentAttempt) {
                $latestAttempt->update([
                    'status' => $this->mapAttemptStatus($providerStatus),
                    'payment_method' => $paymentMethod,
                    'fee' => $providerFee,
                    'total_payment' => $totalPayment,
                    'payment_number' => $paymentNumber,
                    'expired_at' => $expiredAt,
                ]);
            }

            PaymentEvent::query()->create([
                'payment_attempt_id' => $latestAttempt?->id,
                'payment_id' => $payment->id,
                'invoice_id' => $invoice->id,
                'provider' => 'pakasir',
                'event_source' => 'status_check',
                'provider_order_id' => $invoice->invoice_number,
                'provider_status' => $providerStatus,
                'payload' => json_encode($detail, JSON_THROW_ON_ERROR),
                'headers' => null,
                'is_verified' => true,
                'verification_notes' => 'Status diverifikasi via transaction detail API.',
                'received_at' => now(),
                'processed_at' => now(),
            ]);

            $invoice->update([
                'status' => $this->mapInvoiceStatus($localStatus),
                'paid_at' => $localStatus === 'paid' ? ($completedAt ?? now()) : null,
            ]);

            if ($bookingRequest instanceof BookingRequest) {
                $bookingRequest->update([
                    'payment_status' => $localStatus,
                    'status' => $localStatus === 'expired' ? 'expired' : $bookingRequest->status,
                ]);

                if ($localStatus === 'paid') {
                    $this->createLeaseFromPaidBooking->handle($bookingRequest, $invoice);
                } elseif ($businessExpired && $providerStatus === 'completed') {
                    $this->activityLog->handle(
                        null,
                        $payment,
                        'late-payment-anomaly',
                        'Completed payment arrived after the booking had already expired locally.',
                        [
                            'payment' => $this->activityLog->snapshot($payment->fresh(), [
                                'id',
                                'invoice_id',
                                'provider',
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
                            ]),
                            'booking_request' => $this->activityLog->snapshot($bookingRequest->fresh(), [
                                'id',
                                'status',
                                'payment_status',
                                'payment_due_at',
                                'final_price',
                            ]),
                            'invoice' => $this->activityLog->snapshot($invoice->fresh(), [
                                'id',
                                'invoice_number',
                                'status',
                                'due_date',
                                'total_amount',
                            ]),
                        ],
                    );
                }
            } elseif ($businessExpired && $providerStatus === 'completed') {
                $this->activityLog->handle(
                    null,
                    $payment,
                    'late-payment-anomaly',
                    'Completed payment arrived after the booking had already expired locally.',
                    [
                        'payment' => $this->activityLog->snapshot($payment->fresh(), [
                            'id',
                            'invoice_id',
                            'provider',
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
                        ]),
                        'invoice' => $this->activityLog->snapshot($invoice->fresh(), [
                            'id',
                            'invoice_number',
                            'status',
                            'due_date',
                            'total_amount',
                        ]),
                    ],
                );
            }

            $this->activityLog->handle(
                null,
                $payment,
                'payment-status-synced',
                'Payment status synchronized from Pakasir transaction detail.',
                [
                    'payment' => $this->activityLog->snapshot($payment->fresh(), [
                        'id',
                        'invoice_id',
                        'provider',
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
                    ]),
                    'booking_request' => $bookingRequest instanceof BookingRequest
                        ? $this->activityLog->snapshot($bookingRequest->fresh(), [
                            'id',
                            'status',
                            'payment_status',
                            'payment_due_at',
                            'final_price',
                        ])
                        : null,
                    'invoice' => $this->activityLog->snapshot($invoice->fresh(), [
                        'id',
                        'invoice_number',
                        'status',
                        'due_date',
                        'total_amount',
                    ]),
                ],
            );

            return $payment->refresh();
        });
    }

    protected function isBusinessExpired(Invoice $invoice, ?BookingRequest $bookingRequest): bool
    {
        if ($invoice->status === 'expired') {
            return true;
        }

        return $bookingRequest?->status === 'expired' || $bookingRequest?->payment_status === 'expired';
    }

    protected function mapPaymentStatus(string $providerStatus): string
    {
        return match ($providerStatus) {
            'completed' => 'paid',
            'expired' => 'expired',
            'cancelled' => 'cancelled',
            'failed' => 'failed',
            default => 'pending',
        };
    }

    protected function mapInvoiceStatus(string $paymentStatus): string
    {
        return match ($paymentStatus) {
            'paid' => 'paid',
            'expired' => 'expired',
            'cancelled' => 'cancelled',
            default => 'pending',
        };
    }

    protected function mapAttemptStatus(string $providerStatus): string
    {
        return match ($providerStatus) {
            'completed' => 'completed',
            'expired' => 'expired',
            'cancelled' => 'cancelled',
            'failed' => 'failed',
            default => 'pending',
        };
    }

    protected function failureMessage(string $paymentStatus): ?string
    {
        return match ($paymentStatus) {
            'expired' => 'Pembayaran melewati batas waktu.',
            'failed' => 'Pembayaran gagal diproses oleh gateway.',
            'cancelled' => 'Transaksi dibatalkan.',
            default => null,
        };
    }
}
