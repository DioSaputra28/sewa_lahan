<?php

namespace App\Filament\Resources\Invoices\Schemas;

use App\Models\Invoice;
use Filament\Forms\Components\DatePicker;
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
                    ->description('Informasi dasar invoice ditampilkan agar admin bisa memverifikasi konteks tagihan sebelum melakukan perubahan.')
                    ->schema([
                        Placeholder::make('invoice_number_label')
                            ->label('Nomor invoice')
                            ->content(fn (?Invoice $record): string => $record?->invoice_number ?? '-'),
                        Placeholder::make('customer_name')
                            ->label('Customer')
                            ->content(fn (?Invoice $record): string => $record?->user?->name ?? '-'),
                        Placeholder::make('booking_reference')
                            ->label('Booking request')
                            ->content(fn (?Invoice $record): string => $record?->bookingRequest ? '#'.$record->bookingRequest->id : '-'),
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
                        Placeholder::make('payment_attempt_count')
                            ->label('Jumlah link pembayaran')
                            ->content(fn (?Invoice $record): string => (string) ($record?->paymentAttempts()->count() ?? 0)),
                        Placeholder::make('editing_lock_info')
                            ->label('Status edit')
                            ->content(fn (?Invoice $record): string => $record && $record->paymentAttempts()->exists()
                                ? 'Invoice terkunci karena link pembayaran sudah dibuat.'
                                : 'Invoice masih bisa disesuaikan karena belum ada link pembayaran.'),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 3,
                    ]),
                Section::make('Pengaturan tagihan')
                    ->description('Admin dapat menyesuaikan jatuh tempo, item tagihan, diskon, dan penalti selama invoice belum dipakai untuk membuat link pembayaran.')
                    ->schema([
                        DatePicker::make('due_date')
                            ->label('Jatuh tempo')
                            ->required()
                            ->disabled(fn (?Invoice $record): bool => (bool) $record?->paymentAttempts()->exists())
                            ->helperText('Tentukan batas akhir pembayaran invoice ini.'),
                        TextInput::make('discount_amount')
                            ->label('Diskon')
                            ->prefix('Rp')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->disabled(fn (?Invoice $record): bool => (bool) $record?->paymentAttempts()->exists())
                            ->helperText('Isi diskon bila ada pengurangan harga yang disetujui.'),
                        TextInput::make('penalty_amount')
                            ->label('Penalti')
                            ->prefix('Rp')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->disabled(fn (?Invoice $record): bool => (bool) $record?->paymentAttempts()->exists())
                            ->helperText('Isi penalti jika ada biaya tambahan pada tagihan ini.'),
                        Repeater::make('items')
                            ->relationship('items')
                            ->label('Item invoice')
                            ->addActionLabel('Tambah item')
                            ->disabled(fn (?Invoice $record): bool => (bool) $record?->paymentAttempts()->exists())
                            ->deletable(fn (?Invoice $record): bool => ! $record?->paymentAttempts()->exists())
                            ->addable(fn (?Invoice $record): bool => ! $record?->paymentAttempts()->exists())
                            ->schema([
                                TextInput::make('type')
                                    ->label('Tipe item')
                                    ->placeholder('Contoh: rent')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('description')
                                    ->label('Deskripsi')
                                    ->placeholder('Tulis rincian tagihan')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('qty')
                                    ->label('Qty')
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(1)
                                    ->required(),
                                TextInput::make('unit_price')
                                    ->label('Harga satuan')
                                    ->prefix('Rp')
                                    ->numeric()
                                    ->minValue(0)
                                    ->required(),
                                TextInput::make('total')
                                    ->label('Total item')
                                    ->prefix('Rp')
                                    ->numeric()
                                    ->minValue(0)
                                    ->required()
                                    ->helperText('Isi total item sesuai perhitungan qty x harga satuan.'),
                            ])
                            ->columns([
                                'default' => 1,
                                'md' => 5,
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 3,
                    ]),
                Section::make('Ringkasan nominal')
                    ->description('Ringkasan ini ditampilkan agar admin dapat memeriksa subtotal dan total akhir invoice setelah perubahan dilakukan.')
                    ->schema([
                        TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->prefix('Rp')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($state, ?Invoice $record): string => number_format((int) ($state ?? $record?->subtotal ?? 0), 0, ',', '.')),
                        TextInput::make('total_amount')
                            ->label('Total invoice')
                            ->prefix('Rp')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($state, ?Invoice $record): string => number_format((int) ($state ?? $record?->total_amount ?? 0), 0, ',', '.')),
                        Placeholder::make('payment_summary')
                            ->label('Ringkasan payment')
                            ->content(fn (?Invoice $record): string => $record && $record->paymentAttempts()->exists()
                                ? 'Invoice sudah memiliki link pembayaran. Pastikan tidak ada perubahan nominal lagi sebelum sinkronisasi pembayaran.'
                                : 'Belum ada link pembayaran. Invoice masih dapat disesuaikan.'),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 3,
                    ]),
            ]);
    }
}
