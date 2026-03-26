<?php

it('uses project branding in the global mail html layout', function () {
    $mailLayout = file_get_contents(resource_path('views/vendor/mail/html/layout.blade.php'));

    expect($mailLayout)
        ->toContain('get_site_name()');
});

it('uses project branding in the global mail html header', function () {
    $mailHeader = file_get_contents(resource_path('views/vendor/mail/html/header.blade.php'));

    expect($mailHeader)
        ->toContain('get_site_name()')
        ->toContain('get_site_logo_url()');
});

it('uses project branding in the global mail text template', function () {
    $mailTextMessage = file_get_contents(resource_path('views/vendor/mail/text/message.blade.php'));

    expect($mailTextMessage)
        ->toContain('get_site_name()');
});
