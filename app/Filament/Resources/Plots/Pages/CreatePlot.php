<?php

namespace App\Filament\Resources\Plots\Pages;

use App\Filament\Resources\Plots\PlotResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreatePlot extends CreateRecord
{
    protected static string $resource = PlotResource::class;

    protected Width|string|null $maxContentWidth = Width::Full;
}
