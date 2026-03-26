<?php

namespace App\Console\Commands;

use App\Models\PageViewDailySummary;
use App\Models\PageViewEvent;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RollupPageViewsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analytics:rollup-page-views {--date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollup page view events into daily summaries';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $targetDate = $this->option('date');
        $date = $targetDate
            ? CarbonImmutable::parse((string) $targetDate)->toDateString()
            : CarbonImmutable::today()->toDateString();

        $this->rollupDate($date);

        $this->info("Rolled up page views for {$date}.");

        return self::SUCCESS;
    }

    protected function rollupDate(string $date): void
    {
        $rows = PageViewEvent::query()
            ->selectRaw('DATE(visited_at) as date')
            ->selectRaw('route_name')
            ->selectRaw('page_key')
            ->selectRaw('plot_id')
            ->selectRaw('COUNT(*) as total_views')
            ->selectRaw('COUNT(DISTINCT session_id) as unique_visitors')
            ->whereDate('visited_at', $date)
            ->groupBy([
                DB::raw('DATE(visited_at)'),
                'route_name',
                'page_key',
                'plot_id',
            ])
            ->get();

        PageViewDailySummary::query()->whereDate('date', $date)->delete();

        if ($rows->isEmpty()) {
            return;
        }

        PageViewDailySummary::query()->insert(
            $this->transformRows($rows)
        );
    }

    protected function transformRows(Collection $rows): array
    {
        return $rows
            ->map(function (object $row): array {
                return [
                    'date' => $row->date,
                    'route_name' => $row->route_name,
                    'page_key' => $row->page_key,
                    'plot_id' => $row->plot_id,
                    'total_views' => (int) $row->total_views,
                    'unique_visitors' => (int) $row->unique_visitors,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })
            ->all();
    }
}
