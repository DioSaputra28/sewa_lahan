<?php

namespace App\Http\Controllers;

use App\Actions\Payments\SyncPakasirPaymentStatus;
use App\Models\Payment;
use App\Models\PaymentEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PaymentWebhookController extends Controller
{
    public function __invoke(Request $request, SyncPakasirPaymentStatus $syncPakasirPaymentStatus): JsonResponse
    {
        $payload = $request->validate([
            'amount' => ['required', 'integer'],
            'order_id' => ['required', 'string'],
            'project' => ['required', 'string'],
            'status' => ['required', 'string'],
            'payment_method' => ['nullable', 'string'],
            'completed_at' => ['nullable', 'date'],
        ]);

        if ($payload['project'] !== config('services.pakasir.project_slug')) {
            throw ValidationException::withMessages([
                'project' => 'Project Pakasir tidak cocok.',
            ]);
        }

        $payment = Payment::query()
            ->where('provider', 'pakasir')
            ->where('provider_order_id', $payload['order_id'])
            ->firstOrFail();

        if ((int) $payload['amount'] !== (int) $payment->invoice->total_amount) {
            throw ValidationException::withMessages([
                'amount' => 'Nominal webhook tidak cocok dengan invoice.',
            ]);
        }

        $latestAttempt = $payment->invoice->paymentAttempts()->latest('id')->first();

        PaymentEvent::query()->create([
            'payment_attempt_id' => $latestAttempt?->id,
            'payment_id' => $payment->id,
            'invoice_id' => $payment->invoice_id,
            'provider' => 'pakasir',
            'event_source' => 'webhook',
            'provider_order_id' => $payload['order_id'],
            'provider_status' => $payload['status'],
            'payload' => json_encode($payload, JSON_THROW_ON_ERROR),
            'headers' => json_encode($request->headers->all(), JSON_THROW_ON_ERROR),
            'is_verified' => true,
            'verification_notes' => 'Webhook diterima dan lolos validasi dasar sebelum sinkronisasi detail.',
            'received_at' => now(),
            'processed_at' => now(),
        ]);

        $syncPakasirPaymentStatus->handle($payment);

        return response()->json([
            'status' => 'ok',
        ]);
    }
}
