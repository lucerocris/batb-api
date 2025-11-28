<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;



class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                
                TextColumn::make('user.name')
                        ->label('Customer')
                        ->default('Unknown User'),
                TextColumn::make('order_number'),
                TextColumn::make('payment_method'),
                TextColumn::make('total_amount'),
                
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
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