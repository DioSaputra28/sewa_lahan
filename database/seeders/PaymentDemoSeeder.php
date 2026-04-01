<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentAttempt;
use App\Models\PaymentEvent;
use Database\Seeders\Concerns\GuardsAgainstProductionSeeding;
use Illuminate\Database\Seeder;

class PaymentDemoSeeder extends Seeder
{
    use GuardsAgainstProductionSeeding;

    public function run(): void
    {
        $this->guardAgainstProductionSeeding(static::class);

        $invoices = Invoice::query()->get()->keyBy('invoice_number');

        foreach ($invoices as $invoice) {
            $paymentStatus = match ($invoice->status) {
                'paid' => 'paid',
                'expired' => 'expired',
                default => 'pending',
            };

            $providerStatus = match ($invoice->status) {
                'paid' => 'success',
                'expired' => 'expired',
                default => 'pending',
            };

            $payment = Payment::query()->updateOrCreate(
                [
                    'provider_order_id' => $invoice->invoice_number,
                ],
                [
                    'invoice_id' => $invoice->id,
                    'user_id' => $invoice->user_id,
                    'provider' => 'pakasir',
                    'provider_project_slug' => 'demo-project',
                    'provider_order_id' => $invoice->invoice_number,
                    'provider_status' => $providerStatus,
                    'provider_payment_method' => $paymentStatus === 'paid' ? 'qris' : 'virtual_account',
                    'provider_payment_number' => 'PMT-'.str_pad((string) $invoice->id, 5, '0', STR_PAD_LEFT),
                    'provider_fee' => 2500,
                    'provider_total_payment' => $invoice->total_amount + 2500,
                    'provider_expired_at' => $paymentStatus === 'expired' ? now()->subDay() : now()->addDay(),
                    'provider_completed_at' => $paymentStatus === 'paid' ? now()->subDays(2) : null,
                    'amount' => $invoice->total_amount,
                    'status' => $paymentStatus,
                    'paid_at' => $paymentStatus === 'paid' ? now()->subDays(2) : null,
                    'failure_code' => $paymentStatus === 'expired' ? 'EXPIRED' : null,
                    'failure_message' => $paymentStatus === 'expired' ? 'Pembayaran melewati batas waktu.' : null,
                ],
            );

            $attempt = PaymentAttempt::query()->updateOrCreate(
                [
                    'provider_order_id' => $payment->provider_order_id,
                ],
                [
                    'invoice_id' => $invoice->id,
                    'user_id' => $invoice->user_id,
                    'provider' => 'pakasir',
                    'provider_project_slug' => 'demo-project',
                    'provider_order_id' => $payment->provider_order_id,
                    'payment_method' => $payment->provider_payment_method,
                    'request_amount' => $invoice->total_amount,
                    'fee' => 2500,
                    'total_payment' => $invoice->total_amount + 2500,
                    'payment_number' => $payment->provider_payment_number,
                    'checkout_url' => 'https://checkout.example.test/'.strtolower($payment->provider_order_id),
                    'redirect_url' => 'https://sewa-lahan.test/invoices/'.$invoice->id,
                    'qris_only' => $payment->provider_payment_method === 'qris',
                    'is_sandbox' => true,
                    'status' => match ($paymentStatus) {
                        'paid' => 'completed',
                        'expired' => 'expired',
                        default => 'pending',
                    },
                    'expired_at' => $payment->provider_expired_at,
                    'requested_at' => now()->subDays(2),
                    'last_error_message' => $paymentStatus === 'expired' ? 'Payment attempt kadaluarsa.' : null,
                ],
            );

            PaymentEvent::query()->updateOrCreate(
                [
                    'payment_attempt_id' => $attempt->id,
                    'event_source' => 'webhook',
                ],
                [
                    'payment_attempt_id' => $attempt->id,
                    'payment_id' => $payment->id,
                    'invoice_id' => $invoice->id,
                    'provider' => 'pakasir',
                    'event_source' => 'webhook',
                    'provider_order_id' => $payment->provider_order_id,
                    'provider_status' => $providerStatus,
                    'payload' => json_encode(['status' => $providerStatus, 'invoice' => $invoice->invoice_number], JSON_THROW_ON_ERROR),
                    'headers' => json_encode(['x-demo-event' => 'payment-status'], JSON_THROW_ON_ERROR),
                    'is_verified' => $paymentStatus !== 'pending',
                    'verification_notes' => $paymentStatus === 'paid' ? 'Webhook telah diverifikasi.' : ($paymentStatus === 'expired' ? 'Webhook menunjukkan invoice expired.' : 'Menunggu pembayaran customer.'),
                    'received_at' => now()->subDay(),
                    'processed_at' => now()->subDay(),
                ],
            );
        }
    }
}
