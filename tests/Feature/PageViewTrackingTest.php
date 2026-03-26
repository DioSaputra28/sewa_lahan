<?php

use App\Models\Area;
use App\Models\Market;
use App\Models\PageViewEvent;
use App\Models\Plot;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

it('tracks public page visits and sets visitor session cookie', function () {
    config()->set('analytics.page_views.excluded_environments', []);

    $response = $this->get('/');

    $response->assertSuccessful();
    $response->assertCookie('pv_sid');

    $sessionId = $response->getCookie('pv_sid')?->getValue();

    expect($sessionId)->not->toBeEmpty();
    $this->assertDatabaseCount('page_view_events', 1);
    $this->assertDatabaseHas('page_view_events', [
        'route_name' => 'home',
        'page_key' => 'home',
    ]);
});

it('does not track non scoped routes', function () {
    config()->set('analytics.page_views.excluded_environments', []);

    $this->get('/up')->assertSuccessful();

    $this->assertDatabaseCount('page_view_events', 0);
});

it('reuses existing visitor session cookie value', function () {
    config()->set('analytics.page_views.excluded_environments', []);

    $sessionId = 'session-fixed-123';

    $this->withCookie('pv_sid', $sessionId)
        ->get('/about')
        ->assertSuccessful();

    $event = PageViewEvent::query()->firstOrFail();

    expect($event->session_id)->toBe($sessionId);
});

it('does not track bot user agents', function () {
    config()->set('analytics.page_views.excluded_environments', []);

    $this->withHeader('User-Agent', 'Googlebot/2.1 (+http://www.google.com/bot.html)')
        ->get('/')
        ->assertSuccessful();

    $this->assertDatabaseCount('page_view_events', 0);
});

it('tracks lahan detail route with related plot id', function () {
    config()->set('analytics.page_views.excluded_environments', []);

    $market = Market::query()->create([
        'name' => 'Pasar Induk',
        'address' => 'Jl. Pasar No. 1',
        'city' => 'Kebumen',
        'status' => 'active',
    ]);

    $area = Area::query()->create([
        'market_id' => $market->id,
        'name' => 'Blok A',
        'status' => 'active',
    ]);

    $plot = Plot::query()->create([
        'market_id' => $market->id,
        'area_id' => $area->id,
        'name' => 'Lahan A1',
        'type' => 'kios',
        'length' => 2,
        'width' => 3,
        'area_square_meters' => 6,
        'base_price_monthly' => 1_200_000,
        'base_price_yearly' => 12_000_000,
        'status' => 'available',
    ]);

    $this->get(route('lahan.show', $plot))->assertSuccessful();

    $this->assertDatabaseHas('page_view_events', [
        'route_name' => 'lahan.show',
        'page_key' => 'lahan.show',
        'plot_id' => $plot->id,
    ]);
});

it('rolls up daily summaries with unique visitors deduplicated by session id', function () {
    CarbonImmutable::setTestNow('2026-03-25 12:00:00');

    DB::table('page_view_events')->insert([
        [
            'visited_at' => '2026-03-25 10:00:00',
            'route_name' => 'lahan.index',
            'page_key' => 'lahan.index',
            'path' => '/lahan',
            'plot_id' => null,
            'session_id' => 'sid-a',
            'visitor_hash' => hash('sha256', 'a'),
            'metadata' => json_encode(['source' => 'test']),
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'visited_at' => '2026-03-25 10:03:00',
            'route_name' => 'lahan.index',
            'page_key' => 'lahan.index',
            'path' => '/lahan',
            'plot_id' => null,
            'session_id' => 'sid-a',
            'visitor_hash' => hash('sha256', 'a'),
            'metadata' => json_encode(['source' => 'test']),
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'visited_at' => '2026-03-25 10:05:00',
            'route_name' => 'lahan.index',
            'page_key' => 'lahan.index',
            'path' => '/lahan',
            'plot_id' => null,
            'session_id' => 'sid-b',
            'visitor_hash' => hash('sha256', 'b'),
            'metadata' => json_encode(['source' => 'test']),
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ]);

    Artisan::call('analytics:rollup-page-views', [
        '--date' => '2026-03-25',
    ]);

    $this->assertDatabaseHas('page_view_daily_summaries', [
        'date' => '2026-03-25',
        'route_name' => 'lahan.index',
        'page_key' => 'lahan.index',
        'total_views' => 3,
        'unique_visitors' => 2,
    ]);
});
