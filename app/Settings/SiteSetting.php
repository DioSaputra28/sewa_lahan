<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SiteSetting extends Settings
{
    public string $site_name;

    public ?string $site_logo = null;

    public ?string $favicon = null;

    public ?string $youtube_url = null;

    public ?string $instagram_url = null;

    public ?string $tiktok_url = null;

    public ?string $facebook_url = null;

    public ?string $twitter_x_url = null;

    public ?string $threads_url = null;

    public ?string $landing_hero_image = null;

    public ?string $landing_hero_image_alt = null;

    public ?string $landing_map_embed_url = null;

    public ?string $about_hero_image = null;

    public ?string $about_hero_image_alt = null;

    public ?string $about_genesis_content = null;

    public ?string $office_email = null;

    public ?string $office_whatsapp = null;

    public ?string $office_phone = null;

    public ?string $office_location = null;

    public ?string $office_map_embed_url = null;

    public static function group(): string
    {
        return 'site';
    }
}
