<?php

namespace App\Livewire;

use Filament\Widgets\ChartWidget;

class AnalysisCharts extends ChartWidget
{
    protected ?string $heading = 'Analysis Charts';

    protected function getData(): array
    {
        return [
            //
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
