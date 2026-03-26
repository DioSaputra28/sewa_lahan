<?php

use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\UserPanelProvider;
use App\Settings\SiteSetting;
use Filament\Panel;

it('uses dynamic favicon from site settings in both filament panels', function () {
    $settings = app(SiteSetting::class);
    $settings->favicon = 'https://example.com/favicon.ico';
    $settings->save();

    $expectedFavicon = 'https://example.com/favicon.ico?v='.md5('https://example.com/favicon.ico');

    $adminPanel = (new AdminPanelProvider(app()))->panel(app(Panel::class));
    $userPanel = (new UserPanelProvider(app()))->panel(app(Panel::class));

    expect($adminPanel->getFavicon())->toBe($expectedFavicon)
        ->and($userPanel->getFavicon())->toBe($expectedFavicon);
});

it('falls back to default favicon in both filament panels when setting is empty', function () {
    $settings = app(SiteSetting::class);
    $settings->favicon = null;
    $settings->save();

    $expectedFavicon = '/favicon.ico?v='.md5('/favicon.ico');

    $adminPanel = (new AdminPanelProvider(app()))->panel(app(Panel::class));
    $userPanel = (new UserPanelProvider(app()))->panel(app(Panel::class));

    expect($adminPanel->getFavicon())->toBe($expectedFavicon)
        ->and($userPanel->getFavicon())->toBe($expectedFavicon);
});
