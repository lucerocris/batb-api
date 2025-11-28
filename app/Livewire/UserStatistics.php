<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStatistics extends StatsOverviewWidget
{
    protected static ?string $pollingInterval = null;


    protected function getStats(): array
    {
        return [
            Stats::make('Total Users', '41'),
            Stats::make('Average Orders per user', '4'),
        ];
    }
}
