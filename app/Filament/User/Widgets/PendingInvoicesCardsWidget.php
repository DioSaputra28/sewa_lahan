<?php

namespace App\Filament\User\Widgets;

use App\Filament\User\Resources\Invoices\InvoiceResource;
use App\Models\Invoice;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Auth;

class PendingInvoicesCardsWidget extends TableWidget
{
    protected static bool $isLazy = false;

    protected static ?string $heading = 'Invoice yang Perlu Diselesaikan';

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'lg' => 2,
    ];

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Invoice::query()
                    ->where('user_id', Auth::id())
                    ->whereIn('status', ['unpaid', 'pending', 'expired'])
                    ->with('bookingRequest.plot')
                    ->orderByRaw("case when status = 'expired' then 0 else 1 end")
                    ->orderBy('due_date')
                    ->limit(3),
            )
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('Invoice')
                    ->searchable(),
                TextColumn::make('bookingRequest.plot.name')
                    ->label('Lahan')
                    ->placeholder('-')
                    ->wrap(),
                TextColumn::make('total_amount')
                    ->label('Nominal')
                    ->formatStateUsing(fn (int $state): string => 'Rp '.number_format($state, 0, ',', '.')),
                TextColumn::make('due_date')
                    ->label('Jatuh tempo')
                    ->date('d M Y'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'unpaid' => 'Belum dibayar',
                        'pending' => 'Menunggu pembayaran',
                        'expired' => 'Kadaluarsa',
                        'paid' => 'Lunas',
                        default => ucfirst($state),
                    }),
            ])
            ->recordUrl(fn (Invoice $record): string => InvoiceResource::getUrl('view', ['record' => $record]))
            ->paginated(false)
            ->emptyStateHeading('Belum ada invoice aktif')
            ->emptyStateDescription('Semua tagihanmu sudah selesai atau belum ada invoice yang perlu ditindaklanjuti.')
            ->emptyStateActions([]);
    }
}
