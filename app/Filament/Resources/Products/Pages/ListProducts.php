<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Exports\ProductExporter;
use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Pages\Concerns\ExposesTableToWidgets;

class ListProducts extends ListRecords
{
    use ExposesTableToWidgets;
    protected static string $resource = ProductResource::class;


    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ExportAction::make()
            ->exporter(ProductExporter::class),
        ];
    }


    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\Products\Widgets\ProductStats::class,
        ];
    }
}
