<?php

namespace App\Filament\User\Widgets;

use App\Filament\User\Resources\Bookings\BookingResource;
use App\Models\BookingRequest;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Auth;

class RecentBookingsCardsWidget extends TableWidget
{
    protected static bool $isLazy = false;

    protected static ?string $heading = 'Booking yang Masih Diproses';

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'lg' => 2,
    ];

    public function table(Table $table): Table
    {
        return $table
            ->query(
                BookingRequest::query()
                    ->where('user_id', Auth::id())
                    ->whereIn('status', ['pending', 'approved', 'rejected', 'expired'])
                    ->with('plot.market')
                    ->latest('created_at')
                    ->limit(3),
            )
            ->columns([
                TextColumn::make('plot.name')
                    ->label('Lahan')
                    ->placeholder('-')
                    ->wrap(),
                TextColumn::make('plot.market.name')
                    ->label('Pasar')
                    ->placeholder('-'),
                TextColumn::make('created_at')
                    ->label('Diajukan')
                    ->dateTime('d M Y H:i'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Menunggu review',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        'expired' => 'Kadaluarsa',
                        default => ucfirst($state),
                    }),
            ])
            ->recordUrl(fn (BookingRequest $record): string => BookingResource::getUrl('edit', ['record' => $record]))
            ->paginated(false)
            ->emptyStateHeading('Belum ada booking yang perlu ditindaklanjuti')
            ->emptyStateDescription('Booking terbaru yang masih relevan akan muncul di sini.')
            ->emptyStateActions([]);
    }
}
