<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AverageOrderValueWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 2;

    protected function getStats(): array
    {
        $end = Carbon::now()->endOfMonth();
        $start = (clone $end)->subMonths(11)->startOfMonth();

        $orders = Order::query()
            ->whereBetween('created_at', [$start, $end])
            ->where('payment_status', 'paid')
            ->get(['subtotal']);

        $orderCount = $orders->count();
        $total = $orders->sum('subtotal');
        $avg = $orderCount ? $total / $orderCount : 0;

        return [
            Stat::make('Avg. Order Value', '₱' . number_format($avg, 2))
                ->description('Across paid orders (12 months)')
                ->color('primary'),
        ];
    }
}
