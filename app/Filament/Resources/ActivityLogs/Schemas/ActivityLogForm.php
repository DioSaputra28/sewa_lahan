<?php

namespace App\Filament\Resources\ActivityLogs\Schemas;

use App\Models\ActivityLog;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ActivityLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informasi activity log')
                    ->description('Bagian ini menampilkan ringkasan aksi penting yang dilakukan admin pada sistem.')
                    ->schema([
                        Placeholder::make('actor_name')
                            ->label('Actor')
                            ->content(fn (?ActivityLog $record): string => $record?->actor?->name ?? 'System'),
                        Placeholder::make('action')
                            ->label('Action')
                            ->content(fn (?ActivityLog $record): string => $record?->action ?? '-'),
                        Placeholder::make('target_type')
                            ->label('Target type')
                            ->content(fn (?ActivityLog $record): string => $record?->target_type ?? '-'),
                        Placeholder::make('target_id')
                            ->label('Target ID')
                            ->content(fn (?ActivityLog $record): string => filled($record?->target_id) ? (string) $record->target_id : '-'),
                        Placeholder::make('created_at')
                            ->label('Waktu kejadian')
                            ->content(fn (?ActivityLog $record): string => $record?->created_at?->format('d M Y H:i:s') ?? '-'),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 3,
                    ]),
                Section::make('Deskripsi')
                    ->description('Deskripsi singkat membantu admin memahami konteks aksi tanpa membuka data target terlebih dahulu.')
                    ->schema([
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->disabled()
                            ->dehydrated(false)
                            ->rows(4),
                    ]),
                Section::make('Properties')
                    ->description('Data tambahan ini berguna untuk audit dan investigasi jika ada perubahan penting pada sistem.')
                    ->schema([
                        Textarea::make('properties')
                            ->label('Properties')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn (?array $state): string => filled($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '-')
                            ->rows(8),
                    ]),
            ]);
    }
}
