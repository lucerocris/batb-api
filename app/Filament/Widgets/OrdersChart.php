<?php

namespace App\Filament\Widgets;
use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class OrdersChart extends ChartWidget
{
    protected ?string $heading = 'Amount of sales per month';

    protected function getData(): array
    {

        $sales = Order::selectRaw('MONTH(order_date) as month, SUM(total_amount) as total')
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


  public static function getColumns(): int | array
    {
        return 1;
    }


    protected function getType(): string
    {
        return 'line'; 
    }
}