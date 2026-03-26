<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class GoogleMapsEmbedRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || blank(trim($value))) {
            return;
        }

        $embed = trim($value);

        $isEmbedUrl = preg_match('/^https:\/\/(www\.)?google\.com\/maps\/embed/i', $embed) === 1;
        $isEmbedIframe = preg_match('/<iframe[^>]*\bsrc=["\']https:\/\/(www\.)?google\.com\/maps\/embed[^"\']*["\'][^>]*>.*<\/iframe>/is', $embed) === 1;

        if (! $isEmbedUrl && ! $isEmbedIframe) {
            $fail('Isi dengan URL embed Google Maps atau tag iframe embed yang valid.');
        }
    }
}
