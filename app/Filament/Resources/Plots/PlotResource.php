<?php

namespace App\Filament\Resources\Plots;

use App\Filament\Resources\Plots\Pages\CreatePlot;
use App\Filament\Resources\Plots\Pages\EditPlot;
use App\Filament\Resources\Plots\Pages\ListPlots;
use App\Filament\Resources\Plots\Schemas\PlotForm;
use App\Filament\Resources\Plots\Tables\PlotsTable;
use App\Models\Plot;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class PlotResource extends Resource
{
    protected static ?string $model = Plot::class;

    protected static string|BackedEnum|null $navigationIcon = null;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return PlotForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PlotsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPlots::route('/'),
            'create' => CreatePlot::route('/create'),
            'edit' => EditPlot::route('/{record}/edit'),
        ];
    }
}
