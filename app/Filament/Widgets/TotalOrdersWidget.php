<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Schema;

class TotalOrdersWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 2;

    protected function getStats(): array
    {
        // 12-month range (oldest -> newest)
        $end = Carbon::now()->endOfMonth();
        $start = (clone $end)->subMonths(11)->startOfMonth();

        // set the column your table actually uses
        $statusColumn = 'payment_status';

        // statuses must be strings
        $orderStatuses = ['paid'];

        // safety: check column exists and return an informative stat if not
        if (! Schema::hasColumn('orders', $statusColumn)) {
            return [
                Stat::make('Total Orders', '—')
                    ->description("Column '{$statusColumn}' not found on orders table")
                    ->color('danger'),
            ];
        }

        // group counts by YYYY-MM
        $rows = Order::query()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, COUNT(*) as cnt")
            ->whereBetween('created_at', [$start->toDateString(), $end->toDateString()])
            ->whereIn($statusColumn, $orderStatuses)
            ->groupBy('ym')
            ->orderBy('ym')
            ->get()
            ->pluck('cnt', 'ym')
            ->toArray();

        $chartData = [];
        $period = clone $start;
        while ($period->lte($end)) {
            $key = $period->format('Y-m');
            $chartData[] = isset($rows[$key]) ? (int) $rows[$key] : 0;
            $period->addMonth();
        }

        $totalOrders = array_sum($chartData);

        // last vs previous month diff
        $count = count($chartData);
        $last = $count >= 1 ? $chartData[$count - 1] : 0;
        $prev = $count >= 2 ? $chartData[$count - 2] : 0;
        $diff = $last - $prev;

        $diffText = ($diff >= 0 ? '+' : '') . $diff . ' from previous month';
        $icon = $diff >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down';
        $color = $diff >= 0 ? 'success' : 'danger';

        return [
            Stat::make('Total Orders', number_format($totalOrders))
                ->description($diffText)
                ->descriptionIcon($icon)
                ->chart($chartData)
                ->color($color),
        ];
    }
}
