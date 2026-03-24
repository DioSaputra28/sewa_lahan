<?php

namespace App\Filament\User\Resources\Leases\Schemas;

use App\Filament\User\Resources\Invoices\InvoiceResource;
use App\Models\Lease;
use Carbon\Carbon;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class LeaseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informasi kontrak')
                    ->description('Bagian ini menampilkan ringkasan kontrak aktif atau kontrak yang pernah dimiliki tenant.')
                    ->schema([
                        Placeholder::make('lease_number')
                            ->label('Nomor lease')
                            ->content(fn (?Lease $record): string => $record?->lease_number ?? '-'),
                        Placeholder::make('status')
                            ->label('Status lease')
                            ->content(fn (?Lease $record): string => match ($record?->status) {
                                'draft' => 'Draft',
                                'active' => 'Aktif',
                                'ended' => 'Berakhir',
                                'cancelled' => 'Dibatalkan',
                                default => '-',
                            }),
                        Placeholder::make('activated_at')
                            ->label('Diaktifkan pada')
                            ->content(fn (?Lease $record): string => $record?->activated_at?->format('d M Y H:i') ?? '-'),
                        Placeholder::make('plot_name')
                            ->label('Lahan')
                            ->content(fn (?Lease $record): string => $record?->plot?->name ?? '-'),
                        Placeholder::make('market_name')
                            ->label('Pasar')
                            ->content(fn (?Lease $record): string => $record?->plot?->market?->name ?? '-'),
                        Placeholder::make('invoice_number')
                            ->label('Invoice asal')
                            ->content(function (?Lease $record): string|HtmlString {
                                if (! $record?->invoice) {
                                    return '-';
                                }

                                $url = InvoiceResource::getUrl('view', ['record' => $record->invoice]);

                                return new HtmlString('<a href="'.$url.'" class="text-primary-600 underline underline-offset-4">'.$record->invoice->invoice_number.'</a>');
                            }),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 3,
                    ]),
                Section::make('Periode sewa')
                    ->description('Informasi ini membantu tenant memahami masa aktif kontrak yang sedang berjalan.')
                    ->schema([
                        TextInput::make('term_type')
                            ->label('Tipe sewa')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'monthly' => 'Bulanan',
                                'yearly' => 'Tahunan',
                                default => $state ? ucfirst($state) : '-',
                            }),
                        TextInput::make('duration')
                            ->label('Durasi')
                            ->suffix(' periode')
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('start_date')
                            ->label('Tanggal mulai')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($state, ?Lease $record): string => $record?->start_date?->format('d M Y') ?? '-'),
                        TextInput::make('end_date')
                            ->label('Tanggal selesai')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($state, ?Lease $record): string => $record?->end_date?->format('d M Y') ?? '-'),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ]),
                Section::make('Nilai kontrak')
                    ->description('Nilai kontrak ini berasal dari booking yang telah dibayar dan diaktifkan.')
                    ->schema([
                        TextInput::make('agreed_price')
                            ->label('Harga disepakati')
                            ->prefix('Rp')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($state, ?Lease $record): string => number_format((int) ($state ?? $record?->agreed_price ?? 0), 0, ',', '.')),
                        TextInput::make('deposit_amount')
                            ->label('Deposit')
                            ->prefix('Rp')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($state, ?Lease $record): string => number_format((int) ($state ?? $record?->deposit_amount ?? 0), 0, ',', '.')),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ]),
                Section::make('Lease periods')
                    ->description('Daftar periode kontrak membantu tenant melihat masa sewa dan nominal tiap periodenya.')
                    ->schema([
                        Repeater::make('periods')
                            ->relationship('periods')
                            ->label('Periode kontrak')
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->schema([
                                TextInput::make('period_no')
                                    ->label('Periode ke')
                                    ->disabled()
                                    ->dehydrated(false),
                                TextInput::make('period_start')
                                    ->label('Mulai')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->formatStateUsing(fn ($state): string => filled($state) ? Carbon::parse($state)->format('d M Y') : '-'),
                                TextInput::make('period_end')
                                    ->label('Selesai')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->formatStateUsing(fn ($state): string => filled($state) ? Carbon::parse($state)->format('d M Y') : '-'),
                                TextInput::make('due_date')
                                    ->label('Jatuh tempo')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->formatStateUsing(fn ($state): string => filled($state) ? Carbon::parse($state)->format('d M Y') : '-'),
                                TextInput::make('amount')
                                    ->label('Nominal')
                                    ->prefix('Rp')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->formatStateUsing(fn ($state): string => number_format((int) ($state ?? 0), 0, ',', '.')),
                                TextInput::make('status')
                                    ->label('Status')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                                        'pending' => 'Pending',
                                        'paid' => 'Paid',
                                        'overdue' => 'Overdue',
                                        default => $state ? ucfirst($state) : '-',
                                    }),
                            ])
                            ->columns([
                                'default' => 1,
                                'md' => 3,
                            ])
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
