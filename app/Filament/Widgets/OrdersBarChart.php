<?php

namespace App\Filament\Widgets;


use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\ChartWidget;

class OrdersBarChart extends ChartWidget
{
    protected static ?int $sort = 5;
    protected int|string|array $columnSpan = 3;

    protected ?string $heading = 'Orders made per Month';

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

    protected function getType(): string
    {
        return 'bar';
    }
}
