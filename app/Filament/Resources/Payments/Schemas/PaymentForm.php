<?php

namespace App\Filament\Resources\Payments\Schemas;

use App\Models\Payment;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informasi payment')
                    ->description('Bagian ini menampilkan informasi utama payment yang berasal dari invoice dan payment gateway.')
                    ->schema([
                        Placeholder::make('invoice_number')
                            ->label('Nomor invoice')
                            ->content(fn (?Payment $record): string => $record?->invoice?->invoice_number ?? '-'),
                        Placeholder::make('customer_name')
                            ->label('Customer')
                            ->content(fn (?Payment $record): string => $record?->user?->name ?? '-'),
                        Placeholder::make('provider')
                            ->label('Provider')
                            ->content(fn (?Payment $record): string => $record?->provider ?? '-'),
                        Placeholder::make('provider_order_id')
                            ->label('Provider order ID')
                            ->content(fn (?Payment $record): string => $record?->provider_order_id ?? '-'),
                        Placeholder::make('provider_payment_method')
                            ->label('Metode pembayaran')
                            ->content(fn (?Payment $record): string => $record?->provider_payment_method ?? '-'),
                        Placeholder::make('provider_payment_number')
                            ->label('Nomor pembayaran')
                            ->content(fn (?Payment $record): string => $record?->provider_payment_number ?? '-'),
                        TextInput::make('amount')
                            ->label('Nominal payment')
                            ->prefix('Rp')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($state, ?Payment $record): string => number_format((int) ($state ?? $record?->amount ?? 0), 0, ',', '.')),
                        Placeholder::make('status_label')
                            ->label('Status payment')
                            ->content(function (?Payment $record): HtmlString {
                                [$label, $backgroundColor, $textColor] = match ($record?->status) {
                                    'pending' => ['Pending', '#dbeafe', '#1d4ed8'],
                                    'paid' => ['Paid', '#dcfce7', '#166534'],
                                    'failed' => ['Gagal', '#fee2e2', '#991b1b'],
                                    'expired' => ['Kadaluarsa', '#e2e8f0', '#334155'],
                                    'cancelled' => ['Dibatalkan', '#e2e8f0', '#334155'],
                                    default => ['-', '#f1f5f9', '#475569'],
                                };

                                return self::makeStatusBadge($label, $backgroundColor, $textColor, 'payment_status_badge');
                            }),
                        Placeholder::make('provider_status_label')
                            ->label('Status dari provider')
                            ->content(fn (?Payment $record): string => $record?->provider_status ?? '-'),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 3,
                    ]),
                Section::make('Ringkasan link pembayaran')
                    ->description('Ringkasan ini membantu admin melihat histori singkat link pembayaran yang terhubung ke invoice.')
                    ->schema([
                        Placeholder::make('attempt_count')
                            ->label('Jumlah link')
                            ->content(fn (?Payment $record): string => (string) ($record?->invoice?->paymentAttempts()->count() ?? 0)),
                        Placeholder::make('latest_attempt_status')
                            ->label('Status link terakhir')
                            ->content(function (?Payment $record): HtmlString {
                                $status = $record?->invoice?->paymentAttempts()->latest()->first()?->status;

                                [$label, $backgroundColor, $textColor] = match ($status) {
                                    'pending' => ['Pending', '#dbeafe', '#1d4ed8'],
                                    'paid' => ['Paid', '#dcfce7', '#166534'],
                                    'failed' => ['Failed', '#fee2e2', '#991b1b'],
                                    'expired' => ['Expired', '#e2e8f0', '#334155'],
                                    'cancelled' => ['Cancelled', '#e2e8f0', '#334155'],
                                    default => ['-', '#f1f5f9', '#475569'],
                                };

                                return self::makeStatusBadge($label, $backgroundColor, $textColor, 'latest_attempt_status_badge');
                            }),
                        Placeholder::make('latest_attempt_amount')
                            ->label('Nominal invoice')
                            ->content(fn (?Payment $record): string => 'Rp '.number_format((int) ($record?->invoice?->paymentAttempts()->latest()->first()?->request_amount ?? 0), 0, ',', '.')),
                        Placeholder::make('latest_checkout_url')
                            ->label('Link pembayaran terakhir')
                            ->content(fn (?Payment $record): string => $record?->invoice?->paymentAttempts()->latest()->first()?->checkout_url ?? '-'),
                        Placeholder::make('latest_attempt_expired_at')
                            ->label('Link terakhir expired')
                            ->content(fn (?Payment $record): string => $record?->invoice?->paymentAttempts()->latest()->first()?->expired_at?->format('d M Y H:i') ?? '-'),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ]),
                Section::make('Ringkasan payment event')
                    ->description('Ringkasan event membantu admin melihat jejak callback, status check, dan proses verifikasi payment.')
                    ->schema([
                        Placeholder::make('events_count')
                            ->label('Jumlah event')
                            ->content(fn (?Payment $record): string => (string) ($record?->events()->count() ?? 0)),
                        Placeholder::make('latest_event_source')
                            ->label('Sumber event terakhir')
                            ->content(fn (?Payment $record): string => $record?->events()->latest()->first()?->event_source ?? '-'),
                        Placeholder::make('latest_event_status')
                            ->label('Provider status terakhir')
                            ->content(fn (?Payment $record): string => $record?->events()->latest()->first()?->provider_status ?? '-'),
                        Placeholder::make('latest_event_verified')
                            ->label('Status verifikasi')
                            ->content(fn (?Payment $record): string => ($record?->events()->latest()->first()?->is_verified ?? false) ? 'Terverifikasi' : 'Belum diverifikasi'),
                        Placeholder::make('latest_event_received_at')
                            ->label('Event diterima pada')
                            ->content(fn (?Payment $record): string => $record?->events()->latest()->first()?->received_at?->format('d M Y H:i') ?? '-'),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ]),
                Section::make('Failure info')
                    ->description('Bagian ini membantu admin membaca alasan kegagalan jika payment tidak berhasil diproses.')
                    ->schema([
                        Placeholder::make('failure_code')
                            ->label('Failure code')
                            ->content(fn (?Payment $record): string => $record?->failure_code ?? '-'),
                        Placeholder::make('failure_message')
                            ->label('Failure message')
                            ->content(fn (?Payment $record): string => $record?->failure_message ?? '-'),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 2,
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
