<?php

namespace App\Filament\Resources\Products\Widgets;

use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Filament\Resources\Products\Pages\ListProducts;

class ProductStats extends StatsOverviewWidget
{
    use InteractsWithPageTable;
    
    protected function getTablePage(): string
    {
        return ListProducts::class;  // FIXED
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Total Products', Product::count()),
            Stat::make('Average Product Price', number_format(Product::avg('cost_price'), 2)),
            Stat::make('Current Active, Listed Products', Product::where('is_active', 1)->count()),
        ];
    }
}