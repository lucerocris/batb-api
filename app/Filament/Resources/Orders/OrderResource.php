<?php

namespace App\Filament\Resources\Orders;

use App\Filament\Resources\Orders\Pages\CreateOrder;
use App\Filament\Resources\Orders\Pages\EditOrder;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Filament\Resources\Orders\Pages\ViewOrder;
use App\Filament\Resources\Orders\Schemas\OrderForm;
use App\Filament\Resources\Orders\Tables\OrdersTable;
use Filament\Forms\Components\TextInput;
use Filament\Infolists;
use App\Models\Order;
use UnitEnum;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Table;
use Filament\Infolists\Components\TextEntry;
use Tiptap\Nodes\Text;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ShoppingBag;
    protected static string|UnitEnum|null $navigationGroup = 'Sales';
    protected static ?int $navigationSort = 1; // first within Sales
    public static function form(Schema $schema): Schema
    {
        return OrderForm::configure($schema);
    }


    public static function table(Table $table): Table
    {
        return OrdersTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([

                Grid::make([
                    'default' => 1,
                    'md' => 2,
                    'lg' => 3,
                ])->schema([
                    Group::make([
                        Section::make('Order Overview')
                            ->schema([
                                Grid::make([
                                    'default' => 1,
                                    'md' => 3,
                                ])
                                    ->schema([
                                        // Order Number - Prominent
                                        TextEntry::make('order_number')
                                            ->label('Order Number')
                                            ->weight(FontWeight::Bold)
                                            ->size(TextSize::Large)
                                            ->copyable()
                                            ->icon('heroicon-m-hashtag')
                                            ->columnSpan([
                                                'default' => 1,
                                                'md' => 1,
                                            ]),

                                        // Payment Status
                                        TextEntry::make('payment_status')
                                            ->label('Payment')
                                            ->badge()
                                            ->formatStateUsing(fn(string $state): string => str($state)->headline())
                                            ->color(fn(string $state): string => match ($state) {
                                                'paid' => 'success',
                                                'pending' => 'gray',
                                                'failed' => 'danger',
                                                'refunded' => 'info',
                                                default => 'gray',
                                            })
                                            ->columnSpan([
                                                'default' => 1,
                                                'md' => 1,
                                            ]),

                                        // Fulfillment Status
                                        TextEntry::make('fulfillment_status')
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
                                            })
                                            ->columnSpan([
                                                'default' => 1,
                                                'md' => 1,
                                            ]),
                                    ]),
                            ]),

                        // Items Ordered Section
                        Section::make('Order Items')
                            ->description('Review all items in this order')
                            ->schema([
                                Infolists\Components\RepeatableEntry::make('orderItems')
                                    ->label('')
                                    ->schema([
                                        Grid::make([
                                            'default' => 1,
                                            'md' => 3,
                                        ])
                                            ->schema([
                                                Group::make([
                                                    TextEntry::make('product_name')
                                                        ->label('Product')
                                                        ->weight(FontWeight::Bold)
                                                        ->size(TextSize::Medium),

                                                    TextEntry::make('product_sku')
                                                        ->label('SKU')
                                                        ->color('gray')
                                                        ->size(TextSize::Small)
                                                        ->copyable()
                                                        ->copyMessage('SKU copied!')
                                                        ->copyMessageDuration(1500),

                                                    // Customization inline with product
                                                    TextEntry::make('customization_notes')
                                                        ->label('Customization')
                                                        ->color('warning')
                                                        ->icon('heroicon-o-pencil-square')
                                                        ->placeholder('—')
                                                        ->visible(fn($state) => filled($state)),
                                                ]),


                                                // Pricing Summary (Right-aligned)
                                                Group::make([
                                                    TextEntry::make('quantity')
                                                        ->label('Qty')
                                                        ->badge()
                                                        ->color('info'),

                                                    TextEntry::make('unit_price')
                                                        ->label('Unit Price')
                                                        ->money('PHP'),

                                                    TextEntry::make('line_total')
                                                        ->label('Total')
                                                        ->money('PHP')
                                                        ->weight(FontWeight::Bold)
                                                        ->size(TextSize::Medium)
                                                        ->color('success'),
                                                ])->columns(3)
                                            ])->columns(2)->columnSpan(2)

                                    ]),

                            ]),

                        Section::make('Payment Information')
                        ->schema([
                            Infolists\Components\ImageEntry::make('image_path')
                                ->label('Payment Proof')
                                ->placeholder('No payment proof uploaded.')
                                ->getStateUsing(function ($record) {
                                    if ($record->image_path) {
                                        return asset('storage/' . ltrim($record->image_path, '/'));
                                    }

                                    return $record->image_url;
                                })
                                ->maxWidth(500),
                            TextEntry::make('payment_reference')
                                ->label('Payment Reference')
                                ->copyable(),
                        ])
                    ])->columnSpan(2),


                    Group::make([
                        // Order Details Section
                        Section::make('Order Information')
                            ->schema([
                                Grid::make([
                                    'default' => 1,
                                ])
                                    ->schema([
                                        TextEntry::make('shipping_address_block')
                                            ->label('Shipping Address')
                                            ->state(function (?\App\Models\Order $record): string {
                                                $address = $record?->shipping_address ?? [];
                                                $fullName = trim(($address['first_name'] ?? '') . ' ' . ($address['last_name'] ?? ''));
                                                $addressLine1 = $address['address_line_1'] ?? null;
                                                $addressLine2 = $address['address_line_2'] ?? null;
                                                $barangay = $address['barangay'] ?? null;
                                                $city = $address['city'] ?? null;
                                                $province = $address['province'] ?? null;
                                                $postalCode = $address['postal_code'] ?? null;
                                                $countryCode = isset($address['country_code']) ? strtoupper((string) $address['country_code']) : null;
                                                $phone = $address['phone'] ?? null;

                                                $cityProvince = implode(', ', array_filter([$city, $province], fn($v) => filled($v)));
                                                $cityProvincePostal = trim(implode(' ', array_filter([$cityProvince, $postalCode], fn($v) => filled($v))));

                                                $lines = array_values(array_filter([
                                                    $fullName ?: null,
                                                    $addressLine1,
                                                    $addressLine2,
                                                    $barangay,
                                                    $cityProvincePostal,
                                                    $countryCode,
                                                    $phone,
                                                ], fn($v) => filled($v)));

                                                return implode("\n", $lines);
                                            })
                                            ->formatStateUsing(fn(string $state): string => nl2br(e($state)))
                                            ->html()
                                            ->color('gray')
                                            ->columnSpanFull(),

                                        TextEntry::make('email')
                                            ->copyable(),

                                        TextEntry::make('order_date')
                                            ->label('Order Date')
                                            ->date()
                                            ->icon('heroicon-m-calendar'),

                                        TextEntry::make('payment_method')
                                            ->label('Payment Method')
                                            ->icon('heroicon-m-credit-card')
                                            ->formatStateUsing(fn($state) => strtoupper(str_replace('_', ' ', $state))),

                                        // Add more fields as needed
                                        TextEntry::make('customer_name')
                                            ->label('Customer')
                                            ->icon('heroicon-m-user')
                                            ->visible(fn($state) => filled($state)),

                                    ]),
                            ]),
                        // Order Summary Section (Optional - for totals)
                        Section::make('Order Summary')
                            ->schema([
                                Grid::make([
                                    'default' => 2,
                                ])
                                    ->schema([
                                        TextEntry::make('subtotal')
                                            ->label('Subtotal')
                                            ->money('PHP'),

                                        TextEntry::make('shipping_fee')
                                            ->label('Shipping')
                                            ->money('PHP')
                                            ->visible(fn($state) => filled($state) && $state > 0),
                                        TextEntry::make('discount')
                                            ->label('Discount')
                                            ->money('PHP')
                                            ->visible(fn($state) => filled($state) && $state > 0),

                                    ]),
                            ])
                            ->compact()
                    ])
                ])->columnSpanFull(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'create' => CreateOrder::route('/create'),
            'edit' => EditOrder::route('/{record}/edit'),
            'view' => ViewOrder::route('/{record}'),
        ];
    }
}
