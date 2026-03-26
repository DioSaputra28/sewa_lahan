<?php

use App\Http\Middleware\SetLocale;
use App\Http\Middleware\TrackPublicPageViews;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('booking:expire-overdue')->everyMinute();
        $schedule->command('lease:close-expired')->everyMinute();
        $schedule->command('analytics:rollup-page-views --date='.now()->toDateString())->hourly();
        $schedule->command('analytics:rollup-page-views --date='.now()->subDay()->toDateString())->hourly();
    })
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'webhooks/pakasir',
        ]);
        $middleware->web(append: [
            SetLocale::class,
            TrackPublicPageViews::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
