<?php

it('defines admin navigation groups and assigns resources to them', function () {
    expect(file_get_contents(base_path('app/Providers/Filament/AdminPanelProvider.php')))
        ->toContain('->navigationGroups([')
        ->toContain("NavigationGroup::make('Master Data')")
        ->toContain('->icon(Heroicon::OutlinedSquares2x2)')
        ->toContain("NavigationGroup::make('Transaksi')")
        ->toContain("NavigationGroup::make('Sistem')");

    expect(file_get_contents(base_path('app/Filament/Resources/Markets/MarketResource.php')))
        ->toContain("protected static string|\\UnitEnum|null \$navigationGroup = 'Master Data';");
    expect(file_get_contents(base_path('app/Filament/Resources/Markets/MarketResource.php')))
        ->toContain('protected static string|BackedEnum|null $navigationIcon = null;');
    expect(file_get_contents(base_path('app/Filament/Resources/Areas/AreaResource.php')))
        ->toContain("protected static string|\\UnitEnum|null \$navigationGroup = 'Master Data';");
    expect(file_get_contents(base_path('app/Filament/Resources/Areas/AreaResource.php')))
        ->toContain('protected static string|BackedEnum|null $navigationIcon = null;');
    expect(file_get_contents(base_path('app/Filament/Resources/Plots/PlotResource.php')))
        ->toContain("protected static string|\\UnitEnum|null \$navigationGroup = 'Master Data';");
    expect(file_get_contents(base_path('app/Filament/Resources/Plots/PlotResource.php')))
        ->toContain('protected static string|BackedEnum|null $navigationIcon = null;');

    expect(file_get_contents(base_path('app/Filament/Resources/BookingRequests/BookingRequestResource.php')))
        ->toContain("protected static string|\\UnitEnum|null \$navigationGroup = 'Transaksi';");
    expect(file_get_contents(base_path('app/Filament/Resources/Invoices/InvoiceResource.php')))
        ->toContain("protected static string|\\UnitEnum|null \$navigationGroup = 'Transaksi';");
    expect(file_get_contents(base_path('app/Filament/Resources/Payments/PaymentResource.php')))
        ->toContain("protected static string|\\UnitEnum|null \$navigationGroup = 'Transaksi';");
    expect(file_get_contents(base_path('app/Filament/Resources/Leases/LeaseResource.php')))
        ->toContain("protected static string|\\UnitEnum|null \$navigationGroup = 'Transaksi';");

    expect(file_get_contents(base_path('app/Filament/Resources/Users/UserResource.php')))
        ->toContain("protected static string|\\UnitEnum|null \$navigationGroup = 'Sistem';");
    expect(file_get_contents(base_path('app/Filament/Resources/ActivityLogs/ActivityLogResource.php')))
        ->toContain("protected static string|\\UnitEnum|null \$navigationGroup = 'Sistem';");
});
