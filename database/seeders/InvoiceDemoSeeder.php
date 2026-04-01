<?php

namespace Database\Seeders;

use App\Models\BookingRequest;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Database\Seeders\Concerns\GuardsAgainstProductionSeeding;
use Illuminate\Database\Seeder;

class InvoiceDemoSeeder extends Seeder
{
    use GuardsAgainstProductionSeeding;

    public function run(): void
    {
        $this->guardAgainstProductionSeeding(static::class);

        $bookingRequests = BookingRequest::query()
            ->whereIn('status', ['approved', 'expired'])
            ->get();

        foreach ($bookingRequests as $bookingRequest) {
            $status = match ($bookingRequest->payment_status) {
                'paid' => 'paid',
                'expired' => 'expired',
                default => 'unpaid',
            };

            $invoice = Invoice::query()->updateOrCreate(
                [
                    'booking_request_id' => $bookingRequest->id,
                ],
                [
                    'booking_request_id' => $bookingRequest->id,
                    'user_id' => $bookingRequest->user_id,
                    'invoice_number' => 'INV-DEMO-'.str_pad((string) $bookingRequest->id, 5, '0', STR_PAD_LEFT),
                    'issue_date' => ($bookingRequest->approved_at ?? now())->toDateString(),
                    'due_date' => ($bookingRequest->payment_due_at ?? now()->addDays(3))->toDateString(),
                    'subtotal' => (int) ($bookingRequest->final_price ?? $bookingRequest->quoted_price ?? 0),
                    'discount_amount' => 0,
                    'penalty_amount' => 0,
                    'total_amount' => (int) ($bookingRequest->final_price ?? $bookingRequest->quoted_price ?? 0),
                    'status' => $status,
                    'paid_at' => $status === 'paid' ? now()->subDays(2) : null,
                ],
            );

            InvoiceItem::query()->updateOrCreate(
                [
                    'invoice_id' => $invoice->id,
                    'type' => 'rent',
                ],
                [
                    'invoice_id' => $invoice->id,
                    'type' => 'rent',
                    'description' => 'Sewa '.$bookingRequest->plot?->name.' untuk '.$bookingRequest->duration.' periode',
                    'qty' => 1,
                    'unit_price' => $invoice->total_amount,
                    'total' => $invoice->total_amount,
                ],
            );
        }
    }
}
