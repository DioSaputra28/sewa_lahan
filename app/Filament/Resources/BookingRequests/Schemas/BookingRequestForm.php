<?php

namespace App\Filament\Resources\BookingRequests\Schemas;

use App\Models\BookingRequest;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Illuminate\Support\HtmlString;

class BookingRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Data customer')
                    ->description('Informasi customer ditampilkan sebagai referensi dan tidak dapat diubah dari panel admin.')
                    ->schema([
                        Placeholder::make('customer_name')
                            ->label('Nama customer')
                            ->content(fn (?BookingRequest $record): string => $record?->user?->name ?? '-'),
                        Placeholder::make('customer_email')
                            ->label('Email customer')
                            ->content(fn (?BookingRequest $record): string => $record?->user?->email ?? '-'),
                        Placeholder::make('customer_phone')
                            ->label('Nomor telepon')
                            ->content(fn (?BookingRequest $record): string => $record?->user?->phone ?: '-'),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 3,
                    ]),
                Section::make('Data pengajuan')
                    ->description('Data pengajuan asli dari customer ditampilkan apa adanya untuk membantu proses review.')
                    ->schema([
                        Placeholder::make('plot_name')
                            ->label('Lahan')
                            ->content(fn (?BookingRequest $record): string => $record?->plot?->name ?? '-'),
                        Placeholder::make('market_name')
                            ->label('Pasar')
                            ->content(fn (?BookingRequest $record): string => $record?->plot?->market?->name ?? '-'),
                        Placeholder::make('area_name')
                            ->label('Area / blok')
                            ->content(fn (?BookingRequest $record): string => $record?->plot?->area?->name ?? '-'),
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
                            ->suffix('periode')
                            ->disabled()
                            ->dehydrated(false),
                        DatePicker::make('start_date')
                            ->label('Tanggal mulai')
                            ->disabled()
                            ->dehydrated(false),
                        DatePicker::make('end_date')
                            ->label('Tanggal selesai')
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('quoted_price')
                            ->label('Harga pengajuan')
                            ->prefix('Rp')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($state): string => filled($state) ? number_format((int) $state, 0, ',', '.') : '-'),
                        Textarea::make('notes')
                            ->label('Catatan customer / admin')
                            ->helperText('Admin dapat menambahkan catatan review di sini sebelum pengajuan diproses.')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 3,
                    ]),
                Section::make('Keputusan admin')
                    ->description('Isi bagian ini untuk menentukan nominal final dan batas waktu pembayaran sebelum booking disetujui.')
                    ->schema([
                        TextInput::make('final_price')
                            ->label('Harga final')
                            ->prefix('Rp')
                            ->mask(RawJs::make("\$money(\$input, ',', '.', 0)"))
                            ->stripCharacters('.')
                            ->numeric()
                            ->minValue(0)
                            ->required(fn (?BookingRequest $record): bool => $record?->status === 'pending')
                            ->disabled(fn (?BookingRequest $record): bool => $record?->status !== 'pending')
                            ->helperText('Harga final akan dipakai untuk membuat invoice setelah booking di-approve.'),
                        DateTimePicker::make('payment_due_at')
                            ->label('Batas waktu pembayaran')
                            ->required(fn (?BookingRequest $record): bool => $record?->status === 'pending')
                            ->disabled(fn (?BookingRequest $record): bool => $record?->status !== 'pending')
                            ->seconds(false)
                            ->helperText('Jika lewat dari waktu ini dan belum dibayar, booking dapat dianggap kadaluarsa.'),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ]),
                Section::make('Status dan histori')
                    ->description('Bagian ini membantu admin melihat hasil keputusan dan kondisi pembayaran saat ini.')
                    ->schema([
                        Placeholder::make('booking_status')
                            ->label('Status booking')
                            ->content(function (?BookingRequest $record): HtmlString {
                                [$label, $backgroundColor, $textColor] = match ($record?->status) {
                                    'pending' => ['Pending', '#fef3c7', '#92400e'],
                                    'approved' => ['Approved', '#dcfce7', '#166534'],
                                    'rejected' => ['Rejected', '#fee2e2', '#991b1b'],
                                    'cancelled' => ['Cancelled', '#e2e8f0', '#334155'],
                                    'expired' => ['Expired', '#e2e8f0', '#334155'],
                                    default => ['-', '#f1f5f9', '#475569'],
                                };

                                return self::makeStatusBadge($label, $backgroundColor, $textColor, 'booking_status_badge');
                            }),
                        Placeholder::make('payment_status_label')
                            ->label('Status pembayaran')
                            ->content(function (?BookingRequest $record): HtmlString {
                                [$label, $backgroundColor, $textColor] = match ($record?->payment_status) {
                                    'unpaid' => ['Belum dibayar', '#fef3c7', '#92400e'],
                                    'pending' => ['Menunggu pembayaran', '#dbeafe', '#1d4ed8'],
                                    'paid' => ['Sudah dibayar', '#dcfce7', '#166534'],
                                    'failed' => ['Gagal', '#fee2e2', '#991b1b'],
                                    'cancelled' => ['Dibatalkan', '#e2e8f0', '#334155'],
                                    'expired' => ['Kadaluarsa', '#e2e8f0', '#334155'],
                                    default => ['-', '#f1f5f9', '#475569'],
                                };

                                return self::makeStatusBadge($label, $backgroundColor, $textColor, 'payment_status_badge');
                            }),
                        Placeholder::make('approved_at_label')
                            ->label('Disetujui pada')
                            ->content(fn (?BookingRequest $record): string => $record?->approved_at?->format('d M Y H:i') ?? '-'),
                        Placeholder::make('rejected_at_label')
                            ->label('Ditolak pada')
                            ->content(fn (?BookingRequest $record): string => $record?->rejected_at?->format('d M Y H:i') ?? '-'),
                        Placeholder::make('rejection_reason_label')
                            ->label('Alasan penolakan')
                            ->content(fn (?BookingRequest $record): string => $record?->rejection_reason ?: '-'),
                        Placeholder::make('invoices_count')
                            ->label('Jumlah invoice')
                            ->content(fn (?BookingRequest $record): string => (string) ($record?->invoices()->count() ?? 0)),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 3,
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
