<?php

namespace App\Filament\Resources\Leases\Schemas;

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
                    ->description('Bagian ini menampilkan konteks utama kontrak yang terbentuk setelah payment berhasil diproses.')
                    ->schema([
                        Placeholder::make('lease_number')
                            ->label('Nomor lease')
                            ->content(fn (?Lease $record): string => $record?->lease_number ?? '-'),
                        Placeholder::make('booking_reference')
                            ->label('Booking request')
                            ->content(fn (?Lease $record): string => $record?->bookingRequest ? '#'.$record->bookingRequest->id : '-'),
                        Placeholder::make('invoice_number')
                            ->label('Invoice')
                            ->content(fn (?Lease $record): string => $record?->invoice?->invoice_number ?? '-'),
                        Placeholder::make('tenant_name')
                            ->label('Tenant')
                            ->content(fn (?Lease $record): string => $record?->tenant?->name ?? '-'),
                        Placeholder::make('plot_name')
                            ->label('Lahan')
                            ->content(fn (?Lease $record): string => $record?->plot?->name ?? '-'),
                        Placeholder::make('market_name')
                            ->label('Pasar')
                            ->content(fn (?Lease $record): string => $record?->plot?->market?->name ?? '-'),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 3,
                    ]),
                Section::make('Periode sewa')
                    ->description('Informasi periode ini membantu admin memeriksa masa aktif kontrak dan waktu aktivasi lease.')
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
                        TextInput::make('activated_at')
                            ->label('Diaktifkan pada')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($state, ?Lease $record): string => $record?->activated_at?->format('d M Y H:i') ?? '-'),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 3,
                    ]),
                Section::make('Nilai kontrak')
                    ->description('Nilai ini berasal dari kesepakatan akhir saat booking berhasil dibayar.')
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
                        Placeholder::make('status')
                            ->label('Status lease')
                            ->content(function (?Lease $record): HtmlString {
                                [$label, $backgroundColor, $textColor] = match ($record?->status) {
                                    'draft' => ['Draft', '#e2e8f0', '#334155'],
                                    'active' => ['Aktif', '#dcfce7', '#166534'],
                                    'ended' => ['Berakhir', '#dbeafe', '#1d4ed8'],
                                    'cancelled' => ['Dibatalkan', '#fee2e2', '#991b1b'],
                                    default => ['-', '#f1f5f9', '#475569'],
                                };

                                return self::makeStatusBadge($label, $backgroundColor, $textColor, 'lease_status_badge');
                            }),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 3,
                    ]),
                Section::make('Ringkasan lease periods')
                    ->description('Periode kontrak ditampilkan langsung agar admin bisa memantau tagihan dan status setiap periode sewa.')
                    ->schema([
                        Repeater::make('periods')
                            ->relationship('periods')
                            ->label('Lease periods')
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

    protected static function makeStatusBadge(string $label, string $backgroundColor, string $textColor, string $testId): HtmlString
    {
        return new HtmlString(
            "<span data-testid=\"{$testId}\" style=\"display:inline-flex;align-items:center;border-radius:9999px;padding:0.25rem 0.625rem;font-size:0.75rem;font-weight:700;line-height:1;background:{$backgroundColor};color:{$textColor};\">{$label}</span>"
        );
    }
}
