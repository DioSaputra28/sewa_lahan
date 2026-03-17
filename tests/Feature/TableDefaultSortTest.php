<?php

it('uses newest-first ordering for all filament tables', function () {
    $tables = [
        'app/Filament/Resources/ActivityLogs/Tables/ActivityLogsTable.php',
        'app/Filament/Resources/BookingRequests/Tables/BookingRequestsTable.php',
        'app/Filament/Resources/Invoices/Tables/InvoicesTable.php',
        'app/Filament/Resources/Leases/Tables/LeasesTable.php',
        'app/Filament/Resources/Payments/Tables/PaymentsTable.php',
        'app/Filament/Resources/Plots/Tables/PlotsTable.php',
        'app/Filament/Resources/Users/Tables/UsersTable.php',
        'app/Filament/User/Resources/Bookings/Tables/BookingsTable.php',
        'app/Filament/User/Resources/Invoices/Tables/InvoicesTable.php',
        'app/Filament/User/Resources/Leases/Tables/LeasesTable.php',
    ];

    foreach ($tables as $path) {
        expect(file_get_contents(base_path($path)))
            ->toContain("->defaultSort('created_at', 'desc')");
    }
});
