<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;



class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // ImageColumn::make('image_preview')
                //     ->label('Payment Proof')
                //     ->toggleable()
                //     ->getStateUsing(function ($record) {
                //         if ($record->image_path) {
                //             return assets('storage/' . ltrim($record->image_path, '/'));
                //         }

                //         return $record->image_url;
                //     }),
                TextColumn::make('shipping_address')
                    ->label('Customer')
                    ->searchable()
                    ->getStateUsing(function ($record) {
                        $address = is_array($record->shipping_address) ? $record->shipping_address : json_decode($record->shipping_address, true);
                        $firstName = $address['first_name'] ?? '';
                        $lastName = $address['last_name'] ?? '';
                        return trim("$firstName $lastName") ?: 'Unknown User';
                    }),
                TextColumn::make('order_number')
                ->searchable(),
                TextColumn::make('payment_method')
                    ->formatStateUsing(fn(string $state) => str($state)->replace('_', ' ')->title()),
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
                TextColumn::make('subtotal')->money('php')->sortable(),
                TextColumn::make('order_date')
                    ->label('Order Date')
                    ->getStateUsing(fn ($record) => $record->order_date ?? $record->created_at)
                    ->date()
                    ->sortable(),

            ])
            ->filters([
                SelectFilter::make('payment_status')
                    ->options([
                        'paid' => 'Paid',
                        'pending' => 'Pending',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                    ])
                    ->label('Payment Status'),
                SelectFilter::make('fulfillment_status')
                    ->options([
                        'pending' => 'Pending',
                        'fulfilled' => 'Fulfilled',
                    ])
            ])

            ->recordActions([
                ViewAction::make(),
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}


/*

   protected $fillable = [
        'id',
        'user_id',
        'order_number',
        'status',
        'fulfillment_status',
        'payment_status',
        'payment_method',
        'payment_due_date',
        'payment_sent_date',
        'payment_verified_date',
        'payment_verified_by',
        'expires_at',
        'payment_instructions',
        'payment_reference',
        'idempotency_key',
        'subtotal',
        'tax_amount',
        'phone_number',
        'shipping_amount',
        'discount_amount',
        'total_amount',
        'email',
        'refunded_amount',
        'currency',
        'shipping_address',
        'billing_address',
        'admin_notes',
        'customer_notes',
        'reminder_sent_count',
        'last_reminder_sent',
        'order_date',
        'tip',
    ];
*/
