<?php

namespace App\Filament\User\Resources\Bookings\Schemas;

use App\Models\BookingRequest;
use App\Models\Plot;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class BookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Hidden::make('plot_id')
                    ->default(fn (?BookingRequest $record): ?int => $record?->plot_id ?? request()->integer('plot'))
                    ->required(),
                Section::make('Lahan yang dipilih')
                    ->description('Lahan dipilih dari halaman daftar lahan, lalu dikunci saat membuat booking agar pengajuan tetap konsisten.')
                    ->schema([
                        Placeholder::make('plot_name')
                            ->label('Nama lahan')
                            ->content(fn (?BookingRequest $record): string => selectedPlot($record)?->name ?? '-'),
                        Placeholder::make('market_name')
                            ->label('Pasar')
                            ->content(fn (?BookingRequest $record): string => selectedPlot($record)?->market?->name ?? '-'),
                        Placeholder::make('area_name')
                            ->label('Area / blok')
                            ->content(fn (?BookingRequest $record): string => selectedPlot($record)?->area?->name ?? '-'),
                        Placeholder::make('plot_size')
                            ->label('Ukuran')
                            ->content(fn (?BookingRequest $record): string => selectedPlot($record) ? number_format((float) selectedPlot($record)?->length, 2, ',', '.').' x '.number_format((float) selectedPlot($record)?->width, 2, ',', '.').' m' : '-'),
                        Placeholder::make('plot_area')
                            ->label('Luas')
                            ->content(fn (?BookingRequest $record): string => selectedPlot($record) ? number_format((float) selectedPlot($record)?->area_square_meters, 2, ',', '.').' m2' : '-'),
                        Placeholder::make('plot_prices')
                            ->label('Harga tersedia')
                            ->content(function (?BookingRequest $record, Get $get): string {
                                $plot = static::getSelectedPlot($get('plot_id'), $record);

                                if (! $plot) {
                                    return '-';
                                }

                                $parts = [];

                                if ($plot->base_price_monthly) {
                                    $parts[] = 'Bulanan: Rp '.number_format((int) $plot->base_price_monthly, 0, ',', '.');
                                }

                                if ($plot->base_price_yearly) {
                                    $parts[] = 'Tahunan: Rp '.number_format((int) $plot->base_price_yearly, 0, ',', '.');
                                }

                                return implode(' | ', $parts);
                            })
                            ->columnSpanFull(),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 3,
                    ]),
                Section::make('Pengajuan booking')
                    ->description('Isi data booking untuk menghitung estimasi harga dan periode sewa yang ingin diajukan.')
                    ->schema([
                        Select::make('term_type')
                            ->label('Tipe sewa')
                            ->options(fn (?BookingRequest $record, Get $get): array => static::getTermTypeOptions(
                                plotId: $get('plot_id'),
                                record: $record,
                            ))
                            ->required()
                            ->live()
                            ->disabledOn('edit')
                            ->native(false),
                        TextInput::make('duration')
                            ->label('Durasi')
                            ->helperText('Isi jumlah periode sesuai tipe sewa yang dipilih.')
                            ->numeric()
                            ->minValue(1)
                            ->required()
                            ->live()
                            ->disabledOn('edit'),
                        DatePicker::make('start_date')
                            ->label('Tanggal mulai')
                            ->required()
                            ->disabledOn('edit'),
                        Placeholder::make('quoted_price_preview')
                            ->label('Estimasi harga')
                            ->content(function (?BookingRequest $record, Get $get): string {
                                if ($record?->quoted_price) {
                                    return 'Rp '.number_format((int) $record->quoted_price, 0, ',', '.');
                                }

                                return 'Rp '.static::getQuotedPricePreview(
                                    plotId: $get('plot_id'),
                                    termType: $get('term_type'),
                                    duration: $get('duration'),
                                    record: $record,
                                );
                            })
                            ->extraAttributes([
                                'class' => 'rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-800',
                            ]),
                        Textarea::make('notes')
                            ->label('Catatan tambahan')
                            ->placeholder('Tulis catatan jika ada kebutuhan khusus untuk pengajuan ini')
                            ->rows(4),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ]),
                Section::make('Status booking')
                    ->description('Setelah booking diajukan, bagian ini membantu user melihat keputusan admin dan tindak lanjut berikutnya.')
                    ->hiddenOn('create')
                    ->schema([
                        Placeholder::make('booking_status')
                            ->label('Status booking')
                            ->content(fn (?BookingRequest $record): string => match ($record?->status) {
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                                'expired' => 'Expired',
                                'cancelled' => 'Cancelled',
                                default => '-',
                            }),
                        Placeholder::make('payment_status')
                            ->label('Status pembayaran')
                            ->content(fn (?BookingRequest $record): string => match ($record?->payment_status) {
                                'unpaid' => 'Belum dibayar',
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'failed' => 'Gagal',
                                'cancelled' => 'Dibatalkan',
                                'expired' => 'Expired',
                                default => '-',
                            }),
                        TextInput::make('final_price')
                            ->label('Harga final')
                            ->prefix('Rp')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($state, ?BookingRequest $record): string => filled($record?->final_price) ? number_format((int) $record->final_price, 0, ',', '.') : '-'),
                        TextInput::make('payment_due_at')
                            ->label('Batas pembayaran')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($state, ?BookingRequest $record): string => $record?->payment_due_at?->format('d M Y H:i') ?? '-'),
                        Textarea::make('rejection_reason')
                            ->label('Alasan penolakan')
                            ->disabled()
                            ->dehydrated(false)
                            ->rows(4)
                            ->hidden(fn (?BookingRequest $record): bool => blank($record?->rejection_reason)),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ]),
            ]);
    }

    public static function getTermTypeOptions(int|string|null $plotId, ?BookingRequest $record = null): array
    {
        $plot = static::getSelectedPlot($plotId, $record);

        if (! $plot) {
            return [];
        }

        $options = [];

        if ($plot->base_price_monthly) {
            $options['monthly'] = 'Bulanan';
        }

        if ($plot->base_price_yearly) {
            $options['yearly'] = 'Tahunan';
        }

        return $options;
    }

    public static function getSelectedPlot(int|string|null $plotId, ?BookingRequest $record = null): ?Plot
    {
        $resolvedPlotId = $record?->plot_id ?? $plotId ?? request()->integer('plot');

        if (! $resolvedPlotId) {
            return null;
        }

        return Plot::query()
            ->with(['market', 'area'])
            ->find($resolvedPlotId);
    }

    public static function getQuotedPricePreview(
        int|string|null $plotId,
        ?string $termType,
        int|string|null $duration,
        ?BookingRequest $record = null,
    ): string {
        $plot = static::getSelectedPlot($plotId, $record);
        $resolvedDuration = filled($duration) ? (int) $duration : null;

        if (! $plot || blank($termType) || blank($resolvedDuration) || $resolvedDuration < 1) {
            return '-';
        }

        $basePrice = match ($termType) {
            'monthly' => $plot->base_price_monthly,
            'yearly' => $plot->base_price_yearly,
            default => null,
        };

        if (! $basePrice) {
            return '-';
        }

        return number_format((int) $basePrice * $resolvedDuration, 0, ',', '.');
    }
}

function selectedPlot(?BookingRequest $record): ?Plot
{
    return BookingForm::getSelectedPlot(plotId: null, record: $record);
}
