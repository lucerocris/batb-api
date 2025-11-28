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
        
        return [
            Stat::make('Total users', User::count()),
            Stat::make('Total average orders per User', '2.4'),
        ];
    }
}
