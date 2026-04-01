<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\BookingRequest;
use App\Models\BookingStatusEvent;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Lease;
use App\Models\LeasePeriod;
use App\Models\Payment;
use App\Models\PaymentAttempt;
use App\Models\PaymentEvent;
use App\Models\Plot;
use App\Models\User;
use Database\Seeders\Concerns\GuardsAgainstProductionSeeding;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CustomerUserTransactionSeeder extends Seeder
{
    use GuardsAgainstProductionSeeding;

    public function run(): void
    {
        $this->guardAgainstProductionSeeding(static::class);

        $customer = User::query()->where('email', 'user@gmail.com')->firstOrFail();
        $admin = User::query()->where('email', 'admin@gmail.com')->firstOrFail();
        $plots = Plot::query()
            ->where('status', 'available')
            ->orderBy('name')
            ->get();

        if ($plots->isEmpty()) {
            return;
        }

        DB::transaction(function () use ($admin, $customer, $plots): void {
            $this->purgeExistingTransactions($customer);

            $sequence = 1;

            $sequence = $this->seedBookings(
                customer: $customer,
                admin: $admin,
                plots: $plots,
                sequenceStart: $sequence,
                count: 5,
                scenario: 'pending',
            );

            $sequence = $this->seedBookings(
                customer: $customer,
                admin: $admin,
                plots: $plots,
                sequenceStart: $sequence,
                count: 5,
                scenario: 'rejected',
            );

            $sequence = $this->seedBookings(
                customer: $customer,
                admin: $admin,
                plots: $plots,
                sequenceStart: $sequence,
                count: 4,
                scenario: 'approved_unpaid',
            );

            $sequence = $this->seedBookings(
                customer: $customer,
                admin: $admin,
                plots: $plots,
                sequenceStart: $sequence,
                count: 6,
                scenario: 'approved_pending',
            );

            $sequence = $this->seedBookings(
                customer: $customer,
                admin: $admin,
                plots: $plots,
                sequenceStart: $sequence,
                count: 4,
                scenario: 'expired',
            );

            $this->seedBookings(
                customer: $customer,
                admin: $admin,
                plots: $plots,
                sequenceStart: $sequence,
                count: 6,
                scenario: 'paid',
            );
        });
    }

    protected function purgeExistingTransactions(User $customer): void
    {
        $bookingIds = BookingRequest::query()
            ->where('user_id', $customer->id)
            ->pluck('id');

        $invoiceIds = Invoice::query()
            ->where('user_id', $customer->id)
            ->pluck('id');

        $paymentIds = Payment::query()
            ->where('user_id', $customer->id)
            ->pluck('id');

        $paymentAttemptIds = PaymentAttempt::query()
            ->where('user_id', $customer->id)
            ->pluck('id');

        ActivityLog::query()
            ->where('target_type', BookingRequest::class)
            ->whereIn('target_id', $bookingIds)
            ->delete();

        PaymentEvent::query()
            ->whereIn('invoice_id', $invoiceIds)
            ->orWhereIn('payment_id', $paymentIds)
            ->orWhereIn('payment_attempt_id', $paymentAttemptIds)
            ->delete();

        BookingRequest::query()
            ->where('user_id', $customer->id)
            ->delete();
    }

    protected function seedBookings(
        User $customer,
        User $admin,
        Collection $plots,
        int $sequenceStart,
        int $count,
        string $scenario,
    ): int {
        for ($offset = 0; $offset < $count; $offset++) {
            $sequence = $sequenceStart + $offset;
            $plot = $plots->get(($sequence - 1) % $plots->count());

            $booking = $this->createBooking($customer, $admin, $plot, $sequence, $scenario);

            if (in_array($scenario, ['approved_unpaid', 'approved_pending', 'expired', 'paid'], true)) {
                $invoice = $this->createInvoice($booking, $sequence, $scenario);

                if (in_array($scenario, ['approved_pending', 'expired', 'paid'], true)) {
                    [$payment, $attempt] = $this->createPaymentData($invoice, $sequence, $scenario);
                    $this->createPaymentEvent($invoice, $payment, $attempt, $scenario);
                }

                if ($scenario === 'paid') {
                    $this->createLease($booking, $invoice, $sequence);
                }
            }
        }

        return $sequenceStart + $count;
    }

    protected function createBooking(User $customer, User $admin, Plot $plot, int $sequence, string $scenario): BookingRequest
    {
        [$termType, $duration] = $this->termConfiguration($plot, $sequence);
        $startDate = $this->startDateForScenario($scenario, $sequence);
        $endDate = $this->calculateEndDate($startDate, $termType, $duration);
        $quotedPrice = $this->priceForPlot($plot, $termType, $duration);
        $finalPrice = in_array($scenario, ['approved_unpaid', 'approved_pending', 'expired', 'paid'], true)
            ? max($quotedPrice - (($sequence % 3) * 250000), 1000000)
            : null;

        $booking = BookingRequest::query()->create([
            'user_id' => $customer->id,
            'plot_id' => $plot->id,
            'term_type' => $termType,
            'duration' => $duration,
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'quoted_price' => $quotedPrice,
            'final_price' => $finalPrice,
            'status' => $this->bookingStatus($scenario),
            'payment_status' => $this->bookingPaymentStatus($scenario),
            'approved_by' => in_array($scenario, ['approved_unpaid', 'approved_pending', 'expired', 'paid'], true) ? $admin->id : null,
            'approved_at' => in_array($scenario, ['approved_unpaid', 'approved_pending', 'expired', 'paid'], true) ? $startDate->copy()->subDays(5) : null,
            'rejected_at' => $scenario === 'rejected' ? $startDate->copy()->subDays(3) : null,
            'rejection_reason' => $scenario === 'rejected' ? 'Pengajuan ditolak karena dokumen dan jadwal belum sesuai.' : null,
            'payment_due_at' => in_array($scenario, ['approved_unpaid', 'approved_pending', 'expired', 'paid'], true) ? $startDate->copy()->subDays(1) : null,
            'expires_at' => match ($scenario) {
                'pending' => $startDate->copy()->subDays(2),
                'expired' => $startDate->copy()->subDays(1),
                'approved_unpaid', 'approved_pending', 'paid' => $startDate->copy()->subDays(1),
                'rejected' => $startDate->copy()->subDays(3),
                default => null,
            },
            'notes' => $this->bookingNotes($scenario, $sequence),
        ]);

        $this->createBookingEvents($booking, $admin, $scenario);

        if ($scenario !== 'pending') {
            ActivityLog::query()->create([
                'actor_id' => $admin->id,
                'target_type' => BookingRequest::class,
                'target_id' => $booking->id,
                'action' => $this->activityAction($scenario),
                'description' => $this->activityDescription($scenario, $booking),
                'properties' => json_encode([
                    'status' => $booking->status,
                    'payment_status' => $booking->payment_status,
                    'plot' => $booking->plot?->name,
                ], JSON_THROW_ON_ERROR),
            ]);
        }

        return $booking;
    }

    protected function createBookingEvents(BookingRequest $booking, User $admin, string $scenario): void
    {
        BookingStatusEvent::query()->create([
            'booking_request_id' => $booking->id,
            'status' => 'pending',
            'changed_by' => null,
            'notes' => 'Booking diajukan oleh customer user@gmail.com.',
        ]);

        if (in_array($scenario, ['approved_unpaid', 'approved_pending', 'expired', 'paid'], true)) {
            BookingStatusEvent::query()->create([
                'booking_request_id' => $booking->id,
                'status' => 'approved',
                'changed_by' => $admin->id,
                'notes' => 'Booking di-approve untuk dilanjutkan ke pembayaran.',
            ]);
        }

        if ($scenario === 'rejected') {
            BookingStatusEvent::query()->create([
                'booking_request_id' => $booking->id,
                'status' => 'rejected',
                'changed_by' => $admin->id,
                'notes' => 'Booking ditolak setelah review admin.',
            ]);
        }

        if ($scenario === 'expired') {
            BookingStatusEvent::query()->create([
                'booking_request_id' => $booking->id,
                'status' => 'expired',
                'changed_by' => $admin->id,
                'notes' => 'Booking expired karena pembayaran tidak diselesaikan tepat waktu.',
            ]);
        }
    }

    protected function createInvoice(BookingRequest $booking, int $sequence, string $scenario): Invoice
    {
        $status = match ($scenario) {
            'approved_unpaid' => 'unpaid',
            'approved_pending' => 'pending',
            'expired' => 'expired',
            'paid' => 'paid',
            default => 'unpaid',
        };

        $invoice = Invoice::query()->create([
            'booking_request_id' => $booking->id,
            'user_id' => $booking->user_id,
            'invoice_number' => 'INV-USER-'.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT),
            'issue_date' => ($booking->approved_at ?? now())->toDateString(),
            'due_date' => ($booking->payment_due_at ?? now())->toDateString(),
            'subtotal' => (int) ($booking->final_price ?? $booking->quoted_price ?? 0),
            'discount_amount' => 0,
            'penalty_amount' => 0,
            'total_amount' => (int) ($booking->final_price ?? $booking->quoted_price ?? 0),
            'status' => $status,
            'paid_at' => $status === 'paid' ? now()->subDays($sequence % 5 + 2) : null,
        ]);

        InvoiceItem::query()->create([
            'invoice_id' => $invoice->id,
            'type' => 'rent',
            'description' => 'Sewa '.$booking->plot?->name.' untuk '.$booking->duration.' periode',
            'qty' => 1,
            'unit_price' => $invoice->total_amount,
            'total' => $invoice->total_amount,
        ]);

        return $invoice;
    }

    protected function createPaymentData(Invoice $invoice, int $sequence, string $scenario): array
    {
        $method = $sequence % 2 === 0 ? 'qris' : 'bni_va';
        $providerStatus = match ($scenario) {
            'paid' => 'completed',
            'expired' => 'expired',
            default => 'pending',
        };
        $paymentStatus = match ($scenario) {
            'paid' => 'paid',
            'expired' => 'expired',
            default => 'pending',
        };
        $expiredAt = $scenario === 'expired'
            ? now()->subDays($sequence % 3 + 1)
            : now()->addDays($sequence % 3 + 1);
        $completedAt = $scenario === 'paid'
            ? now()->subDays($sequence % 4 + 2)
            : null;

        $payment = Payment::query()->create([
            'invoice_id' => $invoice->id,
            'user_id' => $invoice->user_id,
            'provider' => 'pakasir',
            'provider_project_slug' => 'demo-project',
            'provider_order_id' => $invoice->invoice_number,
            'provider_status' => $providerStatus,
            'provider_payment_method' => $method,
            'provider_payment_number' => 'PMT-USER-'.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT),
            'provider_fee' => 2500,
            'provider_total_payment' => $invoice->total_amount + 2500,
            'provider_expired_at' => $expiredAt,
            'provider_completed_at' => $completedAt,
            'amount' => $invoice->total_amount,
            'status' => $paymentStatus,
            'paid_at' => $completedAt,
            'failure_code' => $scenario === 'expired' ? 'EXPIRED' : null,
            'failure_message' => $scenario === 'expired' ? 'Pembayaran melewati batas waktu.' : null,
        ]);

        $attempt = PaymentAttempt::query()->create([
            'invoice_id' => $invoice->id,
            'user_id' => $invoice->user_id,
            'provider' => 'pakasir',
            'provider_project_slug' => 'demo-project',
            'provider_order_id' => $invoice->invoice_number,
            'payment_method' => $method,
            'request_amount' => $invoice->total_amount,
            'fee' => 2500,
            'total_payment' => $invoice->total_amount + 2500,
            'payment_number' => 'PMT-USER-'.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT),
            'checkout_url' => 'https://checkout.example.test/'.strtolower($invoice->invoice_number),
            'redirect_url' => 'https://sewa-lahan.test/user/invoices/'.$invoice->id,
            'qris_only' => $method === 'qris',
            'is_sandbox' => true,
            'status' => $providerStatus,
            'expired_at' => $expiredAt,
            'requested_at' => now()->subDays($sequence % 2 + 1),
            'last_error_message' => $scenario === 'expired' ? 'Payment attempt kadaluarsa.' : null,
        ]);

        return [$payment, $attempt];
    }

    protected function createPaymentEvent(Invoice $invoice, Payment $payment, PaymentAttempt $attempt, string $scenario): void
    {
        PaymentEvent::query()->create([
            'payment_attempt_id' => $attempt->id,
            'payment_id' => $payment->id,
            'invoice_id' => $invoice->id,
            'provider' => 'pakasir',
            'event_source' => $scenario === 'approved_pending' ? 'status_check' : 'webhook',
            'provider_order_id' => $payment->provider_order_id,
            'provider_status' => $payment->provider_status,
            'payload' => json_encode([
                'status' => $payment->provider_status,
                'invoice' => $invoice->invoice_number,
                'amount' => $invoice->total_amount,
            ], JSON_THROW_ON_ERROR),
            'headers' => json_encode([
                'x-demo-event' => $scenario,
            ], JSON_THROW_ON_ERROR),
            'is_verified' => $scenario !== 'approved_pending',
            'verification_notes' => match ($scenario) {
                'paid' => 'Pembayaran terverifikasi sukses.',
                'expired' => 'Pembayaran kadaluarsa.',
                default => 'Transaksi masih menunggu pembayaran customer.',
            },
            'received_at' => now()->subHours(6),
            'processed_at' => now()->subHours(6),
        ]);
    }

    protected function createLease(BookingRequest $booking, Invoice $invoice, int $sequence): void
    {
        $lease = Lease::query()->create([
            'booking_request_id' => $booking->id,
            'tenant_id' => $booking->user_id,
            'plot_id' => $booking->plot_id,
            'invoice_id' => $invoice->id,
            'lease_number' => 'LEASE-USER-'.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT),
            'start_date' => $booking->start_date,
            'end_date' => $booking->end_date,
            'term_type' => $booking->term_type,
            'duration' => $booking->duration,
            'agreed_price' => (int) ($booking->final_price ?? $booking->quoted_price ?? 0),
            'deposit_amount' => 0,
            'status' => 'active',
            'activated_at' => now()->subDays($sequence % 3 + 1),
            'renewal_of_lease_id' => null,
        ]);

        $totalAmount = (int) $lease->agreed_price;
        $duration = max((int) $lease->duration, 1);
        $baseAmount = intdiv($totalAmount, $duration);
        $remainder = $totalAmount - ($baseAmount * $duration);
        $startDate = $booking->start_date instanceof Carbon
            ? $booking->start_date->copy()
            : Carbon::parse($booking->start_date);

        for ($period = 1; $period <= $duration; $period++) {
            $periodStart = $lease->term_type === 'monthly'
                ? $startDate->copy()->addMonths($period - 1)
                : $startDate->copy()->addYears($period - 1);

            $periodEnd = $lease->term_type === 'monthly'
                ? $periodStart->copy()->addMonth()->subDay()
                : $periodStart->copy()->addYear()->subDay();

            LeasePeriod::query()->create([
                'lease_id' => $lease->id,
                'period_no' => $period,
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'due_date' => $periodStart,
                'amount' => $baseAmount + ($period === $duration ? $remainder : 0),
                'status' => $period === 1 ? 'paid' : 'pending',
            ]);
        }
    }

    protected function termConfiguration(Plot $plot, int $sequence): array
    {
        if (($sequence % 4 === 0) && filled($plot->base_price_yearly)) {
            return ['yearly', 1];
        }

        return ['monthly', ($sequence % 6) + 1];
    }

    protected function priceForPlot(Plot $plot, string $termType, int $duration): int
    {
        $basePrice = $termType === 'yearly'
            ? (int) ($plot->base_price_yearly ?? 0)
            : (int) ($plot->base_price_monthly ?? 0);

        return $basePrice * $duration;
    }

    protected function startDateForScenario(string $scenario, int $sequence): Carbon
    {
        return Carbon::parse(match ($scenario) {
            'pending' => now()->addDays($sequence + 7)->startOfDay(),
            'rejected' => now()->addDays($sequence + 14)->startOfDay(),
            'approved_unpaid' => now()->addDays($sequence + 5)->startOfDay(),
            'approved_pending' => now()->addDays($sequence + 2)->startOfDay(),
            'expired' => now()->subDays($sequence + 10)->startOfDay(),
            'paid' => now()->subMonths($sequence % 8 + 2)->startOfDay(),
            default => now()->startOfDay(),
        });
    }

    protected function calculateEndDate(Carbon $startDate, string $termType, int $duration): Carbon
    {
        return $termType === 'yearly'
            ? $startDate->copy()->addYears($duration)->subDay()
            : $startDate->copy()->addMonths($duration)->subDay();
    }

    protected function bookingStatus(string $scenario): string
    {
        return match ($scenario) {
            'pending' => 'pending',
            'rejected' => 'rejected',
            'expired' => 'expired',
            default => 'approved',
        };
    }

    protected function bookingPaymentStatus(string $scenario): string
    {
        return match ($scenario) {
            'expired' => 'expired',
            'paid' => 'paid',
            default => 'unpaid',
        };
    }

    protected function bookingNotes(string $scenario, int $sequence): string
    {
        return match ($scenario) {
            'pending' => "Pengajuan #{$sequence} sedang menunggu review admin.",
            'rejected' => "Pengajuan #{$sequence} ditolak setelah review dokumen.",
            'approved_unpaid' => "Pengajuan #{$sequence} sudah disetujui dan invoice sudah terbit.",
            'approved_pending' => "Pengajuan #{$sequence} sudah masuk tahap pembayaran aktif.",
            'expired' => "Pengajuan #{$sequence} kedaluwarsa karena pembayaran tidak selesai.",
            'paid' => "Pengajuan #{$sequence} berhasil dibayar dan kontrak sudah aktif.",
            default => "Pengajuan #{$sequence}.",
        };
    }

    protected function activityAction(string $scenario): string
    {
        return match ($scenario) {
            'rejected' => 'reject-booking',
            'expired' => 'expire-booking',
            'paid' => 'activate-lease',
            default => 'approve-booking',
        };
    }

    protected function activityDescription(string $scenario, BookingRequest $booking): string
    {
        return match ($scenario) {
            'rejected' => 'Admin menolak booking user untuk '.$booking->plot?->name.'.',
            'expired' => 'Admin menandai booking user untuk '.$booking->plot?->name.' sebagai expired.',
            'paid' => 'Admin mengaktifkan lease dari booking user untuk '.$booking->plot?->name.'.',
            default => 'Admin memproses booking user untuk '.$booking->plot?->name.'.',
        };
    }
}
