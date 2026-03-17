<?php

namespace Database\Seeders;

use App\Models\BookingRequest;
use App\Models\BookingStatusEvent;
use App\Models\Plot;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookingFlowDemoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->where('email', 'admin@gmail.com')->firstOrFail();

        $customers = User::query()
            ->whereIn('email', [
                'budi@example.com',
                'siti@example.com',
                'rudi@example.com',
                'dewi@example.com',
                'agus@example.com',
            ])
            ->get()
            ->keyBy('email');

        $plots = Plot::query()
            ->whereIn('name', [
                'Lahan A-01',
                'Lahan A-02',
                'Lahan B-02',
                'Lahan C-01',
                'Lahan C-02',
            ])
            ->get()
            ->keyBy('name');

        $bookings = [
            [
                'email' => 'budi@example.com',
                'plot' => 'Lahan A-01',
                'term_type' => 'monthly',
                'duration' => 12,
                'start_date' => now()->addDays(7)->toDateString(),
                'end_date' => now()->addYear()->addDays(7)->toDateString(),
                'quoted_price' => 180000000,
                'final_price' => null,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'approved_by' => null,
                'approved_at' => null,
                'rejected_at' => null,
                'rejection_reason' => null,
                'payment_due_at' => null,
                'expires_at' => now()->addDays(5),
                'notes' => 'Customer baru mengajukan dan menunggu review admin.',
            ],
            [
                'email' => 'siti@example.com',
                'plot' => 'Lahan A-02',
                'term_type' => 'yearly',
                'duration' => 1,
                'start_date' => now()->addDays(10)->toDateString(),
                'end_date' => now()->addYear()->addDays(10)->toDateString(),
                'quoted_price' => 132000000,
                'final_price' => 130000000,
                'status' => 'approved',
                'payment_status' => 'unpaid',
                'approved_by' => $admin->id,
                'approved_at' => now()->subDay(),
                'rejected_at' => null,
                'rejection_reason' => null,
                'payment_due_at' => now()->addDays(2),
                'expires_at' => now()->addDays(2),
                'notes' => 'Approved dan menunggu pembayaran customer.',
            ],
            [
                'email' => 'rudi@example.com',
                'plot' => 'Lahan B-02',
                'term_type' => 'monthly',
                'duration' => 6,
                'start_date' => now()->addDays(14)->toDateString(),
                'end_date' => now()->addMonths(6)->addDays(14)->toDateString(),
                'quoted_price' => 78000000,
                'final_price' => null,
                'status' => 'rejected',
                'payment_status' => 'unpaid',
                'approved_by' => null,
                'approved_at' => null,
                'rejected_at' => now()->subHours(6),
                'rejection_reason' => 'Dokumen pendukung belum lengkap dan jadwal sewa bertabrakan.',
                'payment_due_at' => null,
                'expires_at' => now()->subHours(6),
                'notes' => 'Pengajuan ditolak oleh admin.',
            ],
            [
                'email' => 'dewi@example.com',
                'plot' => 'Lahan C-01',
                'term_type' => 'monthly',
                'duration' => 3,
                'start_date' => now()->subDays(2)->toDateString(),
                'end_date' => now()->addMonths(3)->subDays(2)->toDateString(),
                'quoted_price' => 27000000,
                'final_price' => 27000000,
                'status' => 'expired',
                'payment_status' => 'expired',
                'approved_by' => $admin->id,
                'approved_at' => now()->subDays(4),
                'rejected_at' => null,
                'rejection_reason' => null,
                'payment_due_at' => now()->subDay(),
                'expires_at' => now()->subDay(),
                'notes' => 'Customer tidak menyelesaikan pembayaran sampai batas waktu berakhir.',
            ],
            [
                'email' => 'agus@example.com',
                'plot' => 'Lahan C-02',
                'term_type' => 'yearly',
                'duration' => 1,
                'start_date' => now()->addDays(3)->toDateString(),
                'end_date' => now()->addYear()->addDays(3)->toDateString(),
                'quoted_price' => 121000000,
                'final_price' => 118000000,
                'status' => 'approved',
                'payment_status' => 'paid',
                'approved_by' => $admin->id,
                'approved_at' => now()->subDays(3),
                'rejected_at' => null,
                'rejection_reason' => null,
                'payment_due_at' => now()->subDays(2),
                'expires_at' => now()->subDays(2),
                'notes' => 'Booking approved dan pembayaran sudah lunas.',
            ],
        ];

        foreach ($bookings as $bookingData) {
            $customer = $customers->get($bookingData['email']);
            $plot = $plots->get($bookingData['plot']);

            $booking = BookingRequest::query()->updateOrCreate(
                [
                    'user_id' => $customer->id,
                    'plot_id' => $plot->id,
                    'start_date' => $bookingData['start_date'],
                ],
                [
                    'user_id' => $customer->id,
                    'plot_id' => $plot->id,
                    'term_type' => $bookingData['term_type'],
                    'duration' => $bookingData['duration'],
                    'start_date' => $bookingData['start_date'],
                    'end_date' => $bookingData['end_date'],
                    'quoted_price' => $bookingData['quoted_price'],
                    'final_price' => $bookingData['final_price'],
                    'status' => $bookingData['status'],
                    'payment_status' => $bookingData['payment_status'],
                    'approved_by' => $bookingData['approved_by'],
                    'approved_at' => $bookingData['approved_at'],
                    'rejected_at' => $bookingData['rejected_at'],
                    'rejection_reason' => $bookingData['rejection_reason'],
                    'payment_due_at' => $bookingData['payment_due_at'],
                    'expires_at' => $bookingData['expires_at'],
                    'notes' => $bookingData['notes'],
                ],
            );

            BookingStatusEvent::query()->updateOrCreate(
                [
                    'booking_request_id' => $booking->id,
                    'status' => $booking->status,
                ],
                [
                    'booking_request_id' => $booking->id,
                    'status' => $booking->status,
                    'changed_by' => $booking->approved_by ?? $admin->id,
                    'notes' => $booking->rejection_reason ?: $booking->notes,
                ],
            );
        }
    }
}
