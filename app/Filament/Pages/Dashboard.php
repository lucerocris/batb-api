<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends BaseDashboard
{
    /**
     * Use a 6-column grid so we can place:
     * - 3 equal-width stat widgets (span 2 each) in the first row
     * - 2 equal-width charts (span 3 each) in the second row
     */
    protected static ?int $navigationSort = 1; // appears first

    public function getColumns(): int | array
    {
        return 6;
    }
}




