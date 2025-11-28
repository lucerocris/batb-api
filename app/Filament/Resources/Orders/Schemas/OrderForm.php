<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Customer')
                    ->relationship('user', 'email')
                    ->searchable()
                    ->getOptionLabelFromRecordUsing(fn (User $record) => $record->getFilamentName())
                    ->preload()
                    ->required(),
                TextInput::make('order_number')
                    ->label('Order Number')
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Select::make('fulfillment_status')
                    ->label('Fulfillment Status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'fulfilled' => 'Fulfilled',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                    ])
                    ->default('unfulfilled'),
                Select::make('payment_status')
                    ->label('Payment Status')
                    ->options([
                        'pending' => 'Pending',
                        'awaiting_confirmation' => 'Awaiting Confirmation',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                    ])
                    ->default('pending')
                    ->required(),
                Select::make('payment_method')
                    ->label('Payment Method')
                    ->options([
                        'bank_transfer' => 'Bank Transfer',
                        'cash' => 'Cash',
                        'card' => 'Card',
                        'mobile_money' => 'Mobile Money',
                        'other' => 'Other',
                    ]),
                TextInput::make('subtotal')
                    ->label('Subtotal')
                    ->numeric()
                    ->prefix('$')
                    ->minValue(0),
                TextInput::make('tax_amount')
                    ->label('Tax Amount')
                    ->numeric()
                    ->prefix('$')
                    ->minValue(0),
                TextInput::make('shipping_amount')
                    ->label('Shipping Amount')
                    ->numeric()
                    ->prefix('$')
                    ->minValue(0),
                TextInput::make('discount_amount')
                    ->label('Discount Amount')
                    ->numeric()
                    ->prefix('$')
                    ->minValue(0),
                TextInput::make('tip')
                    ->label('Tip')
                    ->numeric()
                    ->prefix('$')
                    ->minValue(0),
                TextInput::make('total_amount')
                    ->label('Total Amount')
                    ->numeric()
                    ->prefix('$')
                    ->minValue(0)
                    ->required(),
                TextInput::make('currency')
                    ->default('USD')
                    ->maxLength(3),
                DateTimePicker::make('payment_due_date')
                    ->label('Payment Due Date')
                    ->native(false),
                DateTimePicker::make('payment_sent_date')
                    ->label('Payment Sent Date')
                    ->native(false),
                DateTimePicker::make('payment_verified_date')
                    ->label('Payment Verified Date')
                    ->native(false),
                Select::make('payment_verified_by')
                    ->label('Verified By')
                    ->relationship('verifiedBy', 'email')
                    ->searchable()
                    ->getOptionLabelFromRecordUsing(fn (User $record) => $record->getFilamentName())
                    ->preload(),
                TextInput::make('payment_reference')
                    ->label('Payment Reference')
                    ->maxLength(255),
                FileUpload::make('payment_proof')
                    ->label('Payment Proof')
                    ->image()
                    ->directory('orders/payments')
                    ->disk('public')
                    ->maxSize(2048)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/pdf'])
                    ->columnSpanFull(),
                TextInput::make('email')
                    ->email()
                    ->maxLength(255),
                TextInput::make('phone_number')
                    ->label('Phone Number')
                    ->tel()
                    ->maxLength(20),
                Textarea::make('admin_notes')
                    ->label('Admin Notes')
                    ->rows(3)
                    ->columnSpanFull(),
                Textarea::make('customer_notes')
                    ->label('Customer Notes')
                    ->rows(3)
                    ->columnSpanFull(),
                DateTimePicker::make('order_date')
                    ->label('Order Date')
                    ->native(false)
                    ->default(now()),
                DateTimePicker::make('expires_at')
                    ->label('Expires At')
                    ->native(false),
            ]);
    }
}
