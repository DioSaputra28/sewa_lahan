<?php

use App\Providers\Filament\UserPanelProvider;
use Filament\Panel;

it('registers a dedicated vite theme for the user panel', function () {
    $panel = (new UserPanelProvider(app()))->panel(app(Panel::class));

    expect($panel->getViteTheme())->toBe('resources/css/filament/user/theme.css');
});
