<?php

use App\Settings\SiteSetting;

use function Pest\Laravel\get;

it('extracts google maps embed source from raw values', function () {
    $embedUrl = 'https://www.google.com/maps/embed?pb=test-url';
    $embedIframe = '<iframe src="https://www.google.com/maps/embed?pb=test-iframe" width="600" height="450"></iframe>';
    $invalid = '<iframe src="https://example.com/map"></iframe>';

    expect(extract_google_maps_embed_src($embedUrl))->toBe($embedUrl);
    expect(extract_google_maps_embed_src($embedIframe))->toBe('https://www.google.com/maps/embed?pb=test-iframe');
    expect(extract_google_maps_embed_src($invalid))->toBeNull();
});

it('uses default values when key site settings are empty', function () {
    $settings = app(SiteSetting::class);
    $settings->site_name = '   ';
    $settings->site_logo = null;
    $settings->favicon = null;
    $settings->landing_hero_image = null;
    $settings->about_hero_image = null;
    $settings->about_genesis_content = null;
    $settings->save();

    expect(get_site_name())->toBe(config('app.name'));
    expect(get_site_logo_url())->toBeNull();
    expect(get_favicon_url())->toBe('/favicon.ico');
    expect(get_landing_hero_image_url())->toContain('googleusercontent.com');
    expect(get_about_hero_image_url())->toContain('googleusercontent.com');
    expect(get_about_genesis_content())->toContain(__('web.about.story_p1'));
    expect(get_office_whatsapp_link())->toBeNull();
});

it('renders dynamic settings and hides empty contact items', function () {
    $settings = app(SiteSetting::class);
    $settings->site_name = 'Pasar Dynamic';
    $settings->site_logo = 'https://example.com/logo.png';
    $settings->favicon = 'https://example.com/favicon.ico';
    $settings->landing_hero_image = 'https://example.com/landing-hero.jpg';
    $settings->landing_hero_image_alt = 'Landing hero alt';
    $settings->landing_map_embed_url = '<iframe src="https://www.google.com/maps/embed?pb=landing-map"></iframe>';
    $settings->about_hero_image = 'https://example.com/about-hero.jpg';
    $settings->about_hero_image_alt = 'About hero alt';
    $settings->about_genesis_content = 'Custom genesis content from settings.';
    $settings->office_email = 'office@example.com';
    $settings->office_whatsapp = '+6281234567890';
    $settings->office_phone = null;
    $settings->office_location = null;
    $settings->office_map_embed_url = 'https://www.google.com/maps/embed?pb=office-map';
    $settings->save();

    get('/')
        ->assertSuccessful()
        ->assertSee('Pasar Dynamic')
        ->assertSee('https://example.com/logo.png', escape: false)
        ->assertSee('https://example.com/favicon.ico', escape: false)
        ->assertSee('https://example.com/landing-hero.jpg', escape: false)
        ->assertSee('https://www.google.com/maps/embed?pb=landing-map', escape: false)
        ->assertSee('https://wa.me/6281234567890', escape: false);

    get('/about')
        ->assertSuccessful()
        ->assertSee('https://example.com/about-hero.jpg', escape: false)
        ->assertSee('Custom genesis content from settings.');

    get('/contact')
        ->assertSuccessful()
        ->assertSee('office@example.com')
        ->assertDontSee('+62 21 5550 1234')
        ->assertDontSee('hello@pasarspace.com')
        ->assertSee('https://www.google.com/maps/embed?pb=office-map', escape: false);
});

it('shows social media links on contact page only when configured in site settings', function () {
    $settings = app(SiteSetting::class);
    $settings->youtube_url = 'https://youtube.com/@pasarspace';
    $settings->instagram_url = 'https://instagram.com/pasarspace';
    $settings->tiktok_url = null;
    $settings->facebook_url = null;
    $settings->twitter_x_url = null;
    $settings->threads_url = null;
    $settings->save();

    get('/contact')
        ->assertSuccessful()
        ->assertSee('data-testid="contact-social-links"', escape: false)
        ->assertSee('https://youtube.com/@pasarspace', escape: false)
        ->assertSee('https://instagram.com/pasarspace', escape: false)
        ->assertDontSee('aria-label="TikTok"', escape: false)
        ->assertDontSee('aria-label="Facebook"', escape: false)
        ->assertDontSee('aria-label="Threads"', escape: false)
        ->assertDontSee('aria-label="X"', escape: false);
});

it('hides contact social media section when all social links are empty', function () {
    $settings = app(SiteSetting::class);
    $settings->youtube_url = null;
    $settings->instagram_url = null;
    $settings->tiktok_url = null;
    $settings->facebook_url = null;
    $settings->twitter_x_url = null;
    $settings->threads_url = null;
    $settings->save();

    get('/contact')
        ->assertSuccessful()
        ->assertDontSee('data-testid="contact-social-links"', escape: false)
        ->assertDontSee('aria-label="YouTube"', escape: false)
        ->assertDontSee('aria-label="Instagram"', escape: false)
        ->assertDontSee('aria-label="TikTok"', escape: false)
        ->assertDontSee('aria-label="Facebook"', escape: false)
        ->assertDontSee('aria-label="Threads"', escape: false)
        ->assertDontSee('aria-label="X"', escape: false);
});

it('renders footer market location from office location site setting without social icons', function () {
    $settings = app(SiteSetting::class);
    $settings->office_location = "Jl. Pasar Induk No. 12\nJakarta Timur";
    $settings->save();

    get('/')
        ->assertSuccessful()
        ->assertSee(route('home'), escape: false)
        ->assertSee(route('about'), escape: false)
        ->assertSee(route('lahan.index'), escape: false)
        ->assertSee(route('contact'), escape: false)
        ->assertSee('Lokasi Kantor')
        ->assertSee('Jl. Pasar Induk No. 12')
        ->assertSee('Jakarta Timur')
        ->assertDontSee('aria-label="YouTube"', escape: false)
        ->assertDontSee('aria-label="Instagram"', escape: false)
        ->assertDontSee('aria-label="TikTok"', escape: false)
        ->assertDontSee('aria-label="Facebook"', escape: false)
        ->assertDontSee('aria-label="Threads"', escape: false)
        ->assertDontSee('aria-label="X"', escape: false);
});

it('falls back to lahan link in footer when office location is empty', function () {
    $settings = app(SiteSetting::class);
    $settings->office_location = '   ';
    $settings->save();

    get('/')
        ->assertSuccessful()
        ->assertSee('Lokasi Kantor')
        ->assertSee(route('lahan.index'), escape: false);
});

it('renders footer copyright with dynamic site name only', function () {
    $settings = app(SiteSetting::class);
    $settings->site_name = 'Bibaku Market';
    $settings->save();

    get('/')
        ->assertSuccessful()
        ->assertSee('© '.date('Y').' Bibaku Market. Hak cipta dilindungi.')
        ->assertDontSee('Developed by')
        ->assertDontSee('Bibaku Teknologi')
        ->assertDontSee('href="https://www.bibakuteknologi.com/"', escape: false);
});

it('renders whatsapp contact item as clickable link when whatsapp number is available', function () {
    $settings = app(SiteSetting::class);
    $settings->office_whatsapp = '+6281234567890';
    $settings->save();

    get('/contact')
        ->assertSuccessful()
        ->assertSee('href="https://wa.me/6281234567890"', escape: false)
        ->assertSee('aria-label="WhatsApp"', escape: false)
        ->assertSee('https://cdn.simpleicons.org/whatsapp', escape: false);
});
