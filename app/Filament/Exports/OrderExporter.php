<?php

namespace App\Filament\Exports;

use App\Models\Order;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class OrderExporter extends Exporter
{
    protected static ?string $model = Order::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('order_number')->label('Order Number'),
            ExportColumn::make('customer_name')
                ->label('Customer')
                ->state(fn (Order $record): string => $record->customer_name ?: 'Unknown User'),
            ExportColumn::make('subtotal')->label('Total Amount'),
            ExportColumn::make('fulfillment_status')->label('Fulfillment Status'),
            ExportColumn::make('payment_status')->label('Payment Status'),
            ExportColumn::make('payment_method')->label('Payment Method'),
            ExportColumn::make('shipping_address')->label('Shipping Address'),
            ExportColumn::make('created_at')->label('Created At'),

        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your order export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
