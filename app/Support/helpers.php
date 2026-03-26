<?php

use App\Settings\SiteSetting;
use Illuminate\Support\Facades\Storage;

if (! function_exists('site_setting')) {
    function site_setting(): SiteSetting
    {
        return app(SiteSetting::class);
    }
}

if (! function_exists('normalize_site_setting_value')) {
    function normalize_site_setting_value(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }
}

if (! function_exists('resolve_site_setting_asset_url')) {
    function resolve_site_setting_asset_url(?string $path, ?string $default = null): ?string
    {
        $path = normalize_site_setting_value($path);

        if ($path === null) {
            return $default;
        }

        if (preg_match('/^(https?:)?\/\//i', $path) === 1 || str_starts_with($path, 'data:')) {
            return $path;
        }

        if (str_starts_with($path, '/')) {
            return $path;
        }

        return Storage::url($path);
    }
}

if (! function_exists('get_site_name')) {
    function get_site_name(): string
    {
        return normalize_site_setting_value(site_setting()->site_name) ?? config('app.name', 'PasarSpace');
    }
}

if (! function_exists('get_site_logo_url')) {
    function get_site_logo_url(): ?string
    {
        return resolve_site_setting_asset_url(site_setting()->site_logo);
    }
}

if (! function_exists('get_favicon_url')) {
    function get_favicon_url(): ?string
    {
        return resolve_site_setting_asset_url(site_setting()->favicon, '/favicon.ico');
    }
}

if (! function_exists('get_favicon_cache_key')) {
    function get_favicon_cache_key(): string
    {
        $favicon = normalize_site_setting_value(site_setting()->favicon);

        if ($favicon !== null) {
            return md5($favicon);
        }

        return md5((string) get_favicon_url());
    }
}

if (! function_exists('get_versioned_favicon_url')) {
    function get_versioned_favicon_url(): ?string
    {
        $faviconUrl = get_favicon_url();

        if ($faviconUrl === null) {
            return null;
        }

        $separator = str_contains($faviconUrl, '?') ? '&' : '?';

        return $faviconUrl.$separator.'v='.get_favicon_cache_key();
    }
}

if (! function_exists('get_youtube_url')) {
    function get_youtube_url(): ?string
    {
        return normalize_site_setting_value(site_setting()->youtube_url);
    }
}

if (! function_exists('get_instagram_url')) {
    function get_instagram_url(): ?string
    {
        return normalize_site_setting_value(site_setting()->instagram_url);
    }
}

if (! function_exists('get_tiktok_url')) {
    function get_tiktok_url(): ?string
    {
        return normalize_site_setting_value(site_setting()->tiktok_url);
    }
}

if (! function_exists('get_facebook_url')) {
    function get_facebook_url(): ?string
    {
        return normalize_site_setting_value(site_setting()->facebook_url);
    }
}

if (! function_exists('get_twitter_x_url')) {
    function get_twitter_x_url(): ?string
    {
        return normalize_site_setting_value(site_setting()->twitter_x_url);
    }
}

if (! function_exists('get_threads_url')) {
    function get_threads_url(): ?string
    {
        return normalize_site_setting_value(site_setting()->threads_url);
    }
}

if (! function_exists('get_landing_hero_image_url')) {
    function get_landing_hero_image_url(): string
    {
        return resolve_site_setting_asset_url(
            site_setting()->landing_hero_image,
            'https://lh3.googleusercontent.com/aida-public/AB6AXuCBD-mRgP8BT6LBqFVhYhVqlU4kbrooSw7otxMVS6lUdXJ0kQt3N3qbjF2bWlhNmmvf8p1sxF82qWDU5Njsz4FZgnQvLiZmJ0ceDtCZKYrkKWV8KZbEynB4U-YtfJ--x-btax7JVLliqIzk7MnYQhh247A_PsE1o_3wYQMrWFws-XGCjnH6mN4LZEhJDR8VEKVRO9YyuBOUZaHApbAOqkbXBsZdyct0i8lPts8xD24NjBGeg2N6Q4s0WATNQqfCLxJdq0aqmGrmiIWd'
        );
    }
}

if (! function_exists('get_about_hero_image_url')) {
    function get_about_hero_image_url(): string
    {
        return resolve_site_setting_asset_url(
            site_setting()->about_hero_image,
            'https://lh3.googleusercontent.com/aida-public/AB6AXuA8qFlBXvKqmfV76EvtiiAAweI_2FdkbI15usg7lSEeOGZ_5IpRk6O9SJcbvgzdPRXzM2gUfuOdT7Je9TqBTTP2GGznp8h99vMVChxpeBSlT5s6oR71XA2yFANDldsIKX-2xw-tJSA4isd4IexBkptH757TjC8ckeYTy4-0giDLLtjh7UxYL8CB9yR1pK0IC5HllbZr3BkndZfFjuQYG7zd3JzfYee2L-lRhiIJjwJfegXtFXN49EvPmM3aurrdtzRyXb5olJ6-jHRB'
        );
    }
}

if (! function_exists('get_about_genesis_content')) {
    function get_about_genesis_content(): string
    {
        return normalize_site_setting_value(site_setting()->about_genesis_content)
            ?? (__('web.about.story_p1')."\n\n".__('web.about.story_p2'));
    }
}

if (! function_exists('get_office_email')) {
    function get_office_email(): ?string
    {
        return normalize_site_setting_value(site_setting()->office_email);
    }
}

if (! function_exists('get_office_whatsapp')) {
    function get_office_whatsapp(): ?string
    {
        return normalize_site_setting_value(site_setting()->office_whatsapp);
    }
}

if (! function_exists('get_office_whatsapp_link')) {
    function get_office_whatsapp_link(): ?string
    {
        $whatsapp = get_office_whatsapp();

        if ($whatsapp === null) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $whatsapp);

        if (! is_string($digits) || trim($digits) === '') {
            return null;
        }

        return 'https://wa.me/'.$digits;
    }
}

if (! function_exists('get_office_phone')) {
    function get_office_phone(): ?string
    {
        return normalize_site_setting_value(site_setting()->office_phone);
    }
}

if (! function_exists('get_office_location')) {
    function get_office_location(): ?string
    {
        return normalize_site_setting_value(site_setting()->office_location);
    }
}

if (! function_exists('extract_google_maps_embed_src')) {
    function extract_google_maps_embed_src(?string $raw): ?string
    {
        $raw = normalize_site_setting_value($raw);

        if ($raw === null) {
            return null;
        }

        if (preg_match('/^https:\/\/(www\.)?google\.com\/maps\/embed/i', $raw) === 1) {
            return $raw;
        }

        if (preg_match('/<iframe[^>]*\bsrc=["\']([^"\']+)["\'][^>]*>.*<\/iframe>/is', $raw, $matches) !== 1) {
            return null;
        }

        $src = normalize_site_setting_value($matches[1] ?? null);

        if ($src === null) {
            return null;
        }

        return preg_match('/^https:\/\/(www\.)?google\.com\/maps\/embed/i', $src) === 1 ? $src : null;
    }
}

if (! function_exists('get_landing_map_embed_src')) {
    function get_landing_map_embed_src(): ?string
    {
        return extract_google_maps_embed_src(site_setting()->landing_map_embed_url);
    }
}

if (! function_exists('get_office_map_embed_src')) {
    function get_office_map_embed_src(): ?string
    {
        return extract_google_maps_embed_src(site_setting()->office_map_embed_url);
    }
}

if (! function_exists('has_contact_info')) {
    function has_contact_info(): bool
    {
        return get_office_email() !== null
            || get_office_whatsapp() !== null
            || get_office_phone() !== null
            || get_office_location() !== null;
    }
}
