<?php

namespace App\Console\Commands;

use App\Actions\ActivityLogs\WriteOperationalActivityLog;
use App\Models\Lease;
use App\Notifications\SendLeaseEndedNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class CloseExpiredLeasesCommand extends Command
{
    protected $signature = 'lease:close-expired';

    protected $description = 'Mark active leases as ended when contract end date has passed and notify tenant.';

    public function handle(WriteOperationalActivityLog $writeOperationalActivityLog): int
    {
        Lease::query()
            ->where('status', 'active')
            ->whereDate('end_date', '<', now()->toDateString())
            ->orderBy('id')
            ->chunkById(100, function ($leases) use ($writeOperationalActivityLog): void {
                foreach ($leases as $lease) {
                    $this->closeLease($lease->id, $writeOperationalActivityLog);
                }
            });

        return self::SUCCESS;
    }

    protected function closeLease(int $leaseId, WriteOperationalActivityLog $writeOperationalActivityLog): void
    {
        DB::transaction(function () use ($leaseId, $writeOperationalActivityLog): void {
            $lease = Lease::query()
                ->with(['tenant', 'plot.market'])
                ->whereKey($leaseId)
                ->lockForUpdate()
                ->first();

            if (! $lease instanceof Lease) {
                return;
            }

            if (! $this->shouldEndLease($lease)) {
                return;
            }

            $beforeLease = $writeOperationalActivityLog->snapshot($lease, [
                'id',
                'status',
                'start_date',
                'end_date',
                'tenant_id',
                'plot_id',
            ]);

            $lease->update([
                'status' => 'ended',
            ]);

            $writeOperationalActivityLog->handle(
                null,
                $lease->fresh(),
                'end-lease',
                'Lease ended automatically because contract end date has passed.',
                [
                    'lease' => [
                        'before' => $beforeLease,
                        'after' => $writeOperationalActivityLog->snapshot($lease->fresh(), [
                            'id',
                            'status',
                            'start_date',
                            'end_date',
                            'tenant_id',
                            'plot_id',
                        ]),
                    ],
                ],
            );

            if ($lease->tenant) {
                Notification::send($lease->tenant, new SendLeaseEndedNotification($lease));
            }
        });
    }

    protected function shouldEndLease(Lease $lease): bool
    {
        if ($lease->status !== 'active') {
            return false;
        }

        $endDate = $lease->end_date instanceof Carbon
            ? $lease->end_date->copy()
            : Carbon::parse($lease->end_date);

        return $endDate->lt(now()->startOfDay());
    }
}
