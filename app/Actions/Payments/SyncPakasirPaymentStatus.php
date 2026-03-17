<?php

namespace App\Actions\Payments;

use App\Actions\Leases\CreateLeaseFromPaidBooking;
use App\Models\BookingRequest;
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
    ) {}

    public function handle(Payment $payment): Payment
    {
        $invoice = $payment->invoice()->with(['bookingRequest'])->firstOrFail();
        $detail = $this->pakasirService->getTransactionDetail($invoice);
        $transaction = $detail['transaction'] ?? [];

        return DB::transaction(function () use ($detail, $invoice, $payment, $transaction): Payment {
            $providerStatus = (string) ($transaction['status'] ?? 'pending');
            $paymentMethod = $transaction['payment_method'] ?? $payment->provider_payment_method;
            $paymentNumber = $transaction['payment_number'] ?? $payment->provider_payment_number;
            $providerFee = isset($transaction['fee']) ? (int) $transaction['fee'] : $payment->provider_fee;
            $totalPayment = isset($transaction['total_payment']) ? (int) $transaction['total_payment'] : $payment->provider_total_payment;
            $expiredAt = isset($transaction['expired_at']) ? Carbon::parse($transaction['expired_at']) : $payment->provider_expired_at;
            $completedAt = isset($transaction['completed_at']) ? Carbon::parse($transaction['completed_at']) : null;
            $localStatus = $this->mapPaymentStatus($providerStatus);

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

            $bookingRequest = $invoice->bookingRequest;

            if ($bookingRequest instanceof BookingRequest) {
                $bookingRequest->update([
                    'payment_status' => $localStatus,
                    'status' => $localStatus === 'expired' ? 'expired' : $bookingRequest->status,
                ]);

                if ($localStatus === 'paid') {
                    $this->createLeaseFromPaidBooking->handle($bookingRequest, $invoice);
                }
            }

            return $payment->refresh();
        });
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
