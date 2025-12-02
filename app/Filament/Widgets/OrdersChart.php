<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class OrdersChart extends ChartWidget
{
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = 3;

    protected ?string $heading = 'Sales per Month';

    protected function getData(): array
    {

        $sales = Order::selectRaw('MONTH(order_date) as month, SUM(total_amount) as total')
            ->whereYear('order_date', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $labels = [];
        $data = [];

        for ($m = 1; $m <= 12; $m++) {
            $labels[] = Carbon::create()->month($m)->format('M');
            $data[] = $sales[$m] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Sales (₱)',
                    'data'  => $data,
                ],
            ],
            'labels' => $labels,
        ];
    }



    protected function getType(): string
    {
        return 'line';
    }
}
