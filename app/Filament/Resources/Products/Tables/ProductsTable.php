<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

use Filament\Tables\Columns\TextColumn;


class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                
                TextColumn::make('stock_status'),

                TextColumn::make('name'),
            
                TextColumn::make('sku'),
                
                TextColumn::make('cost_price'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}


/*
'id',
        'category_id',
        'name',
        'slug',
        'description',
        'short_description',
        'sku',
        'base_price',
        'sale_price',
        'cost_price',
        'stock_status',
        'type',
        'image_path',
        'is_active',
        'is_featured',
        'available_from',
        'weight',
        'view_count',
        'purchase_count',
        'average_rating',
        'review_count',
*/