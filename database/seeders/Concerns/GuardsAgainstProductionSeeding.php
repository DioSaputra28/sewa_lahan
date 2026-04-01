<?php

namespace Database\Seeders\Concerns;

use RuntimeException;

trait GuardsAgainstProductionSeeding
{
    protected function guardAgainstProductionSeeding(string $seederName): void
    {
        if (app()->isProduction() || config('app.env') === 'production') {
            throw new RuntimeException("{$seederName} tidak boleh dijalankan di environment production.");
        }
    }
}

