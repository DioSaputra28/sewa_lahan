<?php

it('renders admin infolist statuses with colored badges', function () {
    $bookingRequestForm = file_get_contents(base_path('app/Filament/Resources/BookingRequests/Schemas/BookingRequestForm.php'));
    $invoiceForm = file_get_contents(base_path('app/Filament/Resources/Invoices/Schemas/InvoiceForm.php'));
    $paymentForm = file_get_contents(base_path('app/Filament/Resources/Payments/Schemas/PaymentForm.php'));
    $leaseForm = file_get_contents(base_path('app/Filament/Resources/Leases/Schemas/LeaseForm.php'));

    expect($bookingRequestForm)
        ->toContain("Placeholder::make('booking_status')")
        ->toContain('HtmlString')
        ->toContain('booking_status_badge');

    expect($bookingRequestForm)
        ->toContain("Placeholder::make('payment_status_label')")
        ->toContain('payment_status_badge');

    expect($invoiceForm)
        ->toContain("Placeholder::make('invoice_status_label')")
        ->toContain('invoice_status_badge')
        ->toContain('HtmlString');

    expect($paymentForm)
        ->toContain("Placeholder::make('status_label')")
        ->toContain('payment_status_badge')
        ->toContain('HtmlString');

    expect($leaseForm)
        ->toContain("Placeholder::make('status')")
        ->toContain('lease_status_badge')
        ->toContain('HtmlString');
});
