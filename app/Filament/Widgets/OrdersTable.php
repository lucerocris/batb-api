<?php

namespace App\Filament\Widgets;

use App\Models\Order as ModelsOrder;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Order;

class OrdersTable extends TableWidget
{
    protected static ?int $sort = 5;
    protected int|string|array $columnSpan = 6;
    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => ModelsOrder::query())
            ->columns([
                TextColumn::make('shipping_address')
                    ->label('Customer')
                    ->getStateUsing(function ($record) {
                        $address = is_array($record->shipping_address) ? $record->shipping_address : json_decode($record->shipping_address, true);
                        $firstName = $address['first_name'] ?? '';
                        $lastName = $address['last_name'] ?? '';
                        return trim("$firstName $lastName") ?: 'Unknown User';
                    }),
                TextColumn::make('order_number'),
                TextColumn::make('payment_status')
                    ->label('Payment Status')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => str($state)->headline())
                    ->color(fn(string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'gray',
                        'failed' => 'danger',
                        'refunded' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('fulfillment_status')
                    ->label('Fulfillment')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => str($state)->headline())
                    ->color(fn(string $state): string => match ($state) {
                        'delivered' => 'success',
                        'shipped' => 'info',
                        'fulfilled' => 'primary',
                        'pending' => 'gray',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('subtotal')->money('php'),
                TextColumn::make('order_date')
                    ->label('Order Date')
                    ->getStateUsing(fn ($record) => $record->order_date ?? $record->created_at)
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
