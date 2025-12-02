<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RevenueWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = 2;

    protected function getStats(): array
    {
        // 12-month window (oldest -> newest)
        $end = Carbon::now()->endOfMonth();
        $start = (clone $end)->subMonths(11)->startOfMonth();

        $revenueStatuses = ['paid']; // adjust if needed

        $rows = Order::query()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, SUM(subtotal) as sum")
            ->whereBetween('created_at', [$start->toDateString(), $end->toDateString()])
            ->whereIn('payment_status', $revenueStatuses)
            ->groupBy('ym')
            ->orderBy('ym')
            ->get()
            ->pluck('sum', 'ym')
            ->toArray();

        $chartData = [];
        $period = clone $start;
        while ($period->lte($end)) {
            $key = $period->format('Y-m');
            $chartData[] = isset($rows[$key]) ? (float) $rows[$key] : 0.0;
            $period->addMonth();
        }

        $totalRevenue = array_sum($chartData);

        $count = count($chartData);
        $last = $count >= 1 ? $chartData[$count - 1] : 0;
        $prev = $count >= 2 ? $chartData[$count - 2] : 0;
        $diff = $last - $prev;
        $diffAbs = abs($diff);
        $diffText = '₱' . number_format($diffAbs, 2) . ($diff >= 0 ? ' increase' : ' decrease');
        $icon = $diff >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down';
        $color = $diff >= 0 ? 'success' : 'danger';

        return [
            Stat::make('Total Revenue', '₱' . number_format($totalRevenue, 2))
                ->description($diffText)
                ->descriptionIcon($icon)
                ->chart($chartData)
                ->color($color),
        ];
    }
}
