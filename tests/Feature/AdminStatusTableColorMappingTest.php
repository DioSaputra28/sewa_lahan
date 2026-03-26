<?php

it('defines explicit color mappings for booking status and payment status in admin tables', function () {
    $bookingRequestsTable = file_get_contents(base_path('app/Filament/Resources/BookingRequests/Tables/BookingRequestsTable.php'));
    $invoicesTable = file_get_contents(base_path('app/Filament/Resources/Invoices/Tables/InvoicesTable.php'));
    $paymentsTable = file_get_contents(base_path('app/Filament/Resources/Payments/Tables/PaymentsTable.php'));
    $leasesTable = file_get_contents(base_path('app/Filament/Resources/Leases/Tables/LeasesTable.php'));

    expect($bookingRequestsTable)
        ->toContain("TextColumn::make('status')")
        ->toContain("TextColumn::make('payment_status')")
        ->toContain('->color(fn (string $state): string => match ($state) {')
        ->toContain("'expired' => 'gray'");

    expect($invoicesTable)
        ->toContain("TextColumn::make('status')")
        ->toContain('->color(fn (string $state): string => match ($state) {');

    expect($paymentsTable)
        ->toContain("TextColumn::make('status')")
        ->toContain('->color(fn (string $state): string => match ($state) {');

    expect($leasesTable)
        ->toContain("TextColumn::make('status')")
        ->toContain('->color(fn (?string $state): string => match ($state) {');
});
