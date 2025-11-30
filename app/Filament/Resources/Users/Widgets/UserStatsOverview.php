<?php

namespace App\Filament\Resources\Users\Widgets;

use App\Models\User;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use App\Filament\Resources\Users\Pages\ListUsers;

class UserStatsOverview extends StatsOverviewWidget
{

    use InteractsWithPageTable;

    protected function getTablePage(): string
    {
        return ListUsers::class;
    }
    protected function getStats(): array
    {
        $totalUsers = \App\Models\User::count();
        $totalOrders = \App\Models\Order::count();

        $averageOrders = $totalUsers > 0 ? round($totalOrders / $totalUsers, 2) : 0;
            
        return [
            Stat::make('Total users', User::count()),
            Stat::make('Total average orders per sser', $averageOrders),
            Stat::make('New Users registered today', User::whereDate('created_at', today())->count()),

        ];
    }
}
