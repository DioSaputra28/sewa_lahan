<?php

namespace App\Filament\Resources\Plots\Pages;

use App\Filament\Resources\Plots\PlotResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;

class EditPlot extends EditRecord
{
    protected static string $resource = PlotResource::class;

    protected Width|string|null $maxContentWidth = Width::Full;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Hapus')
                ->requiresConfirmation(),
        ];
    }
}
