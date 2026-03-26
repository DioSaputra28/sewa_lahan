<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->inGroup('site', function ($site): void {
            $site->add('site_name', config('app.name', 'Pasar'));
            $site->add('site_logo', null);
            $site->add('favicon', null);
            $site->add('youtube_url', null);
            $site->add('instagram_url', null);
            $site->add('tiktok_url', null);
            $site->add('facebook_url', null);
            $site->add('twitter_x_url', null);
            $site->add('threads_url', null);
            $site->add('landing_hero_image', null);
            $site->add('landing_hero_image_alt', null);
            $site->add('landing_map_embed_url', null);
            $site->add('about_hero_image', null);
            $site->add('about_hero_image_alt', null);
            $site->add('about_genesis_content', null);
            $site->add('office_email', null);
            $site->add('office_whatsapp', null);
            $site->add('office_phone', null);
            $site->add('office_location', null);
            $site->add('office_map_embed_url', null);
        });
    }
};
