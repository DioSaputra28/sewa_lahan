<?php

namespace App\Actions\Payments;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentAttempt;
use App\Services\PakasirService;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CreatePakasirPaymentAttempt
{
    public function __construct(
        protected PakasirService $pakasirService,
    ) {}

    public function handle(Invoice $invoice): Payment
    {
        if ($invoice->status === 'paid') {
            throw new RuntimeException('Invoice yang sudah dibayar tidak bisa dibuatkan link pembayaran baru.');
        }

        $checkoutUrl = $this->pakasirService->paymentUrl($invoice);

        return DB::transaction(function () use ($checkoutUrl, $invoice): Payment {
            $payment = Payment::query()->updateOrCreate(
                [
                    'provider_order_id' => $invoice->invoice_number,
                ],
                [
                    'invoice_id' => $invoice->id,
                    'user_id' => $invoice->user_id,
                    'provider' => 'pakasir',
                    'provider_project_slug' => $this->pakasirService->projectSlug(),
                    'provider_order_id' => $invoice->invoice_number,
                    'provider_status' => 'pending',
                    'provider_payment_method' => null,
                    'provider_payment_number' => null,
                    'provider_fee' => null,
                    'provider_total_payment' => null,
                    'provider_expired_at' => null,
                    'provider_completed_at' => null,
                    'amount' => $invoice->total_amount,
                    'status' => 'pending',
                    'paid_at' => null,
                    'failure_code' => null,
                    'failure_message' => null,
                ],
            );

            PaymentAttempt::query()->updateOrCreate(
                [
                    'invoice_id' => $invoice->id,
                    'provider_order_id' => $invoice->invoice_number,
                ],
                [
                    'invoice_id' => $invoice->id,
                    'user_id' => $invoice->user_id,
                    'provider' => 'pakasir',
                    'provider_project_slug' => $this->pakasirService->projectSlug(),
                    'provider_order_id' => $invoice->invoice_number,
                    'payment_method' => null,
                    'request_amount' => $invoice->total_amount,
                    'fee' => null,
                    'total_payment' => null,
                    'payment_number' => null,
                    'checkout_url' => $checkoutUrl,
                    'redirect_url' => null,
                    'qris_only' => false,
                    'is_sandbox' => $this->pakasirService->isSandbox(),
                    'status' => 'pending',
                    'expired_at' => null,
                    'requested_at' => now(),
                    'last_error_message' => null,
                ],
            );

            if ($invoice->status === 'unpaid') {
                $invoice->update(['status' => 'pending']);
            }

            return $payment->refresh();
        });
    }
}
