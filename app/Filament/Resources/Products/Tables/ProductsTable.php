<?php

namespace App\Filament\Resources\Products\Tables;

use App\Filament\Resources\Products\Pages\ViewProduct;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;


class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_preview')
                    ->size(40)
                    ->label('Image')
                    ->getStateUsing(function ($record) {
                        if ($record->image_path) {
                            return asset('storage/' . ltrim($record->image_path, '/'));
                        }

                        return $record->image_url;
                    }),
                TextColumn::make('stock_status'),
                TextColumn::make('name'),
                TextColumn::make('sku'),
                TextColumn::make('is_active')
                ->label('Status')
                ->formatStateUsing(fn ($state) => $state ? 'Active' : 'Inactive'),
                TextColumn::make('cost_price')
                    ->label('Cost Price')
                    ->formatStateUsing(fn ($state) => $state ? '$' . number_format($state, 2) : '—'),

            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ])
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
