<?php

use App\Filament\Resources\PageViewDailySummaries\Pages\ListPageViewDailySummaries;
use App\Filament\Widgets\PageViewsStatsWidget;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('allows admin users to open page view analytics list in filament', function () {
    $role = Role::query()->create([
        'name' => 'admin',
    ]);

    $admin = User::factory()->create();
    $admin->roles()->attach($role);

    actingAs($admin);

    get('/admin/page-view-analytics')
        ->assertSuccessful();
});

it('registers tracking summary widget on analytics list page', function () {
    DB::table('page_view_daily_summaries')->insert([
        'date' => now()->toDateString(),
        'route_name' => 'home',
        'page_key' => 'home',
        'plot_id' => null,
        'total_views' => 10,
        'unique_visitors' => 8,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $role = Role::query()->create([
        'name' => 'admin',
    ]);

    $admin = User::factory()->create();
    $admin->roles()->attach($role);

    actingAs($admin);

    get('/admin/page-view-analytics')->assertSuccessful();

    $page = app(ListPageViewDailySummaries::class);

    expect($page->getHeaderWidgets())->toContain(PageViewsStatsWidget::class);
});
