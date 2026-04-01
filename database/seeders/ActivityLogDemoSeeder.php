<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\BookingRequest;
use App\Models\Invoice;
use App\Models\Lease;
use App\Models\Payment;
use App\Models\User;
use Database\Seeders\Concerns\GuardsAgainstProductionSeeding;
use Illuminate\Database\Seeder;

class ActivityLogDemoSeeder extends Seeder
{
    use GuardsAgainstProductionSeeding;

    public function run(): void
    {
        $this->guardAgainstProductionSeeding(static::class);

        $admin = User::query()->where('email', 'admin@gmail.com')->first();

        if (! $admin) {
            return;
        }

        $approvedBooking = BookingRequest::query()->where('status', 'approved')->first();
        $rejectedBooking = BookingRequest::query()->where('status', 'rejected')->first();
        $invoice = Invoice::query()->where('status', 'unpaid')->first();
        $payment = Payment::query()->where('status', 'paid')->first();
        $lease = Lease::query()->where('status', 'active')->first();

        $logs = [
            [
                'actor_id' => $admin->id,
                'target_type' => BookingRequest::class,
                'target_id' => $approvedBooking?->id,
                'action' => 'approve-booking',
                'description' => 'Admin menyetujui booking request dan memicu pembuatan invoice.',
                'properties' => json_encode(['status' => 'approved'], JSON_THROW_ON_ERROR),
            ],
            [
                'actor_id' => $admin->id,
                'target_type' => BookingRequest::class,
                'target_id' => $rejectedBooking?->id,
                'action' => 'reject-booking',
                'description' => 'Admin menolak booking request dengan alasan tertentu.',
                'properties' => json_encode(['status' => 'rejected'], JSON_THROW_ON_ERROR),
            ],
            [
                'actor_id' => $admin->id,
                'target_type' => Invoice::class,
                'target_id' => $invoice?->id,
                'action' => 'review-invoice',
                'description' => 'Admin meninjau invoice aktif untuk memastikan nominal dan jatuh tempo benar.',
                'properties' => json_encode(['status' => $invoice?->status], JSON_THROW_ON_ERROR),
            ],
            [
                'actor_id' => $admin->id,
                'target_type' => Payment::class,
                'target_id' => $payment?->id,
                'action' => 'review-payment',
                'description' => 'Admin meninjau payment yang sudah berhasil dibayar.',
                'properties' => json_encode(['status' => $payment?->status], JSON_THROW_ON_ERROR),
            ],
            [
                'actor_id' => $admin->id,
                'target_type' => Lease::class,
                'target_id' => $lease?->id,
                'action' => 'activate-lease',
                'description' => 'Sistem mengaktifkan kontrak setelah payment sukses.',
                'properties' => json_encode(['status' => $lease?->status], JSON_THROW_ON_ERROR),
            ],
        ];

        foreach ($logs as $log) {
            if (! $log['target_id']) {
                continue;
            }

            ActivityLog::query()->updateOrCreate(
                [
                    'action' => $log['action'],
                    'target_type' => $log['target_type'],
                    'target_id' => $log['target_id'],
                ],
                $log,
            );
        }
    }
}
