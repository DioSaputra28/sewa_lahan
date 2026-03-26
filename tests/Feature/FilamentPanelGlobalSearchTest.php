<?php

use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\UserPanelProvider;
use Filament\Panel;

it('disables global search in admin panel', function () {
    $panel = (new AdminPanelProvider(app()))->panel(app(Panel::class));

    expect($panel->getGlobalSearchProvider())->toBeNull();
});

it('disables global search in user panel', function () {
    $panel = (new UserPanelProvider(app()))->panel(app(Panel::class));

    expect($panel->getGlobalSearchProvider())->toBeNull();
});
