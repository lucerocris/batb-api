<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Order;
use Carbon\Carbon;

class OrdersBarChart extends ChartWidget
{
    protected ?string $heading = 'Amount of orders made per month';

    protected function getType(): string
    {
        return 'bar';
    }
    public static function getColumns(): int | array
    {
        return 1; 
    }

    protected function getData(): array
    {
        $orders = Order::selectRaw('MONTH(order_date) as month, COUNT(*) as count')
            ->whereNotNull('order_date')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');


        $labels = [];
        $data = [];

        for ($m = 1; $m <= 12; $m++) {
            $labels[] = Carbon::create()->month($m)->format('M');
            $data[] = $orders[$m] ?? 0; 
        }

        return [
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data'  => $data,
                ],
            ],
            'labels' => $labels,
        ];
    }
}