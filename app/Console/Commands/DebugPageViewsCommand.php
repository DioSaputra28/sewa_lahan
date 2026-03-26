<?php

namespace App\Console\Commands;

use App\Models\PageViewDailySummary;
use App\Models\PageViewEvent;
use Illuminate\Console\Command;

class DebugPageViewsCommand extends Command
{
    protected $signature = 'analytics:debug-page-views {--date=}';

    protected $description = 'Show runtime diagnostics for page view tracking';

    public function handle(): int
    {
        $date = (string) ($this->option('date') ?: now()->toDateString());

        $this->components->twoColumnDetail('Environment', app()->environment());
        $this->components->twoColumnDetail('Tracking enabled', config('analytics.page_views.enabled', true) ? 'yes' : 'no');
        $this->components->twoColumnDetail('Debug mode', config('analytics.page_views.debug', false) ? 'yes' : 'no');
        $this->components->twoColumnDetail('Excluded envs', json_encode(config('analytics.page_views.excluded_environments', [])) ?: '[]');
        $this->components->twoColumnDetail('Date', $date);

        $rawToday = PageViewEvent::query()->whereDate('visited_at', $date)->count();
        $summaryToday = PageViewDailySummary::query()->whereDate('date', $date)->count();

        $this->newLine();
        $this->components->twoColumnDetail('Raw events (date)', (string) $rawToday);
        $this->components->twoColumnDetail('Summary rows (date)', (string) $summaryToday);

        $latest = PageViewEvent::query()->latest('id')->first();
        $this->newLine();

        if (! $latest) {
            $this->components->warn('No page_view_events found.');

            return self::SUCCESS;
        }

        $this->components->info('Latest raw event:');
        $this->line('id='.$latest->id.' route='.$latest->route_name.' path='.$latest->path.' plot_id='.(string) ($latest->plot_id ?? 'null').' at='.$latest->visited_at?->toDateTimeString());

        return self::SUCCESS;
    }
}
