<?php

namespace App\Filament\User\Resources\Invoices\Schemas;

use App\Models\Invoice;
use App\Models\PaymentAttempt;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informasi invoice')
                    ->schema([
                        Placeholder::make('invoice_number_label')
                            ->label('Nomor invoice')
                            ->content(fn (?Invoice $record): string => $record?->invoice_number ?? '-'),
                        Placeholder::make('invoice_status_label')
                            ->label('Status invoice')
                            ->content(fn (?Invoice $record): string => match ($record?->status) {
                                'unpaid' => 'Belum dibayar',
                                'pending' => 'Pending',
                                'paid' => 'Sudah dibayar',
                                'expired' => 'Kadaluarsa',
                                'cancelled' => 'Dibatalkan',
                                default => '-',
                            }),
                        Placeholder::make('issue_date_label')
                            ->label('Tanggal terbit')
                            ->content(fn (?Invoice $record): string => $record?->issue_date?->format('d M Y') ?? '-'),
                        Placeholder::make('due_date_label')
                            ->label('Jatuh tempo')
                            ->content(fn (?Invoice $record): string => $record?->due_date?->format('d M Y') ?? '-'),
                        Placeholder::make('paid_at_label')
                            ->label('Dibayar pada')
                            ->content(fn (?Invoice $record): string => $record?->paid_at?->format('d M Y H:i') ?? '-'),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 3,
                    ]),
                Section::make('Konteks sewa')
                    ->schema([
                        Placeholder::make('booking_reference')
                            ->label('Booking request')
                            ->content(fn (?Invoice $record): string => $record?->bookingRequest ? '#'.$record->bookingRequest->id : '-'),
                        Placeholder::make('plot_name')
                            ->label('Lahan')
                            ->content(fn (?Invoice $record): string => $record?->bookingRequest?->plot?->name ?? '-'),
                        Placeholder::make('market_name')
                            ->label('Pasar')
                            ->content(fn (?Invoice $record): string => $record?->bookingRequest?->plot?->market?->name ?? '-'),
                        Placeholder::make('area_name')
                            ->label('Area')
                            ->content(fn (?Invoice $record): string => $record?->bookingRequest?->plot?->area?->name ?? '-'),
                        Placeholder::make('term_type')
                            ->label('Tipe sewa')
                            ->content(fn (?Invoice $record): string => match ($record?->bookingRequest?->term_type) {
                                'monthly' => 'Bulanan',
                                'yearly' => 'Tahunan',
                                default => '-',
                            }),
                        Placeholder::make('duration')
                            ->label('Durasi')
                            ->content(fn (?Invoice $record): string => $record?->bookingRequest ? "{$record->bookingRequest->duration} periode" : '-'),
                        Placeholder::make('start_date')
                            ->label('Tanggal mulai')
                            ->content(fn (?Invoice $record): string => $record?->bookingRequest?->start_date?->format('d M Y') ?? '-'),
                        Placeholder::make('end_date')
                            ->label('Tanggal selesai')
                            ->content(fn (?Invoice $record): string => $record?->bookingRequest?->end_date?->format('d M Y') ?? '-'),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 4,
                    ]),
                Section::make('Ringkasan nominal')
                    ->schema([
                        TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->prefix('Rp')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($state, ?Invoice $record): string => number_format((int) ($state ?? $record?->subtotal ?? 0), 0, ',', '.')),
                        TextInput::make('discount_amount')
                            ->label('Diskon')
                            ->prefix('Rp')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($state, ?Invoice $record): string => number_format((int) ($state ?? $record?->discount_amount ?? 0), 0, ',', '.')),
                        TextInput::make('penalty_amount')
                            ->label('Penalti')
                            ->prefix('Rp')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($state, ?Invoice $record): string => number_format((int) ($state ?? $record?->penalty_amount ?? 0), 0, ',', '.')),
                        TextInput::make('total_amount')
                            ->label('Total invoice')
                            ->prefix('Rp')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($state, ?Invoice $record): string => number_format((int) ($state ?? $record?->total_amount ?? 0), 0, ',', '.')),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 4,
                    ]),
                Section::make('Item invoice')
                    ->schema([
                        Repeater::make('items')
                            ->relationship('items')
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->schema([
                                TextInput::make('type')
                                    ->label('Tipe')
                                    ->disabled()
                                    ->dehydrated(false),
                                TextInput::make('description')
                                    ->label('Deskripsi')
                                    ->disabled()
                                    ->dehydrated(false),
                                TextInput::make('qty')
                                    ->label('Qty')
                                    ->disabled()
                                    ->dehydrated(false),
                                TextInput::make('unit_price')
                                    ->label('Harga satuan')
                                    ->prefix('Rp')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->formatStateUsing(fn ($state): string => number_format((int) ($state ?? 0), 0, ',', '.')),
                                TextInput::make('total')
                                    ->label('Total')
                                    ->prefix('Rp')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->formatStateUsing(fn ($state): string => number_format((int) ($state ?? 0), 0, ',', '.')),
                            ])
                            ->columns([
                                'default' => 1,
                                'md' => 5,
                            ])
                            ->columnSpanFull(),
                    ]),
                Section::make('Link pembayaran terakhir')
                    ->schema([
                        Placeholder::make('latest_attempt_status')
                            ->label('Status pembayaran')
                            ->content(fn (?Invoice $record): string => paymentAttemptSummary($record?->latestPaymentAttemptRecord())),
                        Placeholder::make('latest_attempt_method')
                            ->label('Metode pembayaran')
                            ->content(fn (?Invoice $record): string => $record?->latestPaymentAttemptRecord()?->payment_method ?? '-'),
                        Placeholder::make('latest_attempt_number')
                            ->label('Nomor pembayaran')
                            ->content(fn (?Invoice $record): string => $record?->latestPaymentAttemptRecord()?->payment_number ?? '-'),
                        Placeholder::make('latest_attempt_amount')
                            ->label('Nominal invoice')
                            ->content(fn (?Invoice $record): string => currencyLabel($record?->latestPaymentAttemptRecord()?->request_amount)),
                        Placeholder::make('latest_attempt_total')
                            ->label('Total pembayaran')
                            ->content(fn (?Invoice $record): string => currencyLabel($record?->latestPaymentAttemptRecord()?->total_payment)),
                        Placeholder::make('latest_attempt_expired')
                            ->label('Expired at')
                            ->content(fn (?Invoice $record): string => $record?->latestPaymentAttemptRecord()?->expired_at?->format('d M Y H:i') ?? '-'),
                        Placeholder::make('latest_attempt_checkout')
                            ->label('Link pembayaran')
                            ->content(fn (?Invoice $record): string => $record?->latestPaymentAttemptRecord()?->checkout_url ?? '-')
                            ->columnSpanFull(),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 3,
                    ]),
            ]);
    }
}

function paymentAttemptSummary(?PaymentAttempt $attempt): string
{
    if (! $attempt instanceof PaymentAttempt) {
        return 'Belum ada link pembayaran';
    }

    return match ($attempt->status) {
        'pending' => $attempt->isExpired() ? 'Kadaluarsa' : 'Menunggu pembayaran',
        'completed' => 'Sudah dibayar',
        'failed' => 'Gagal',
        'cancelled' => 'Dibatalkan',
        'expired' => 'Kadaluarsa',
        default => ucfirst($attempt->status),
    };
}

function currencyLabel(int|string|null $amount): string
{
    return 'Rp '.number_format((int) ($amount ?? 0), 0, ',', '.');
}
