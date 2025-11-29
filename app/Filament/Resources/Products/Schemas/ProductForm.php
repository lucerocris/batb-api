<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
             
        return $schema
            ->components([
                Section::make('Create a product')
                ->description('Add a new product to the database!')
                ->schema([
                TextInput::make('name')
                    ->label('Product Name')
                    ->filled(),    
                Select::make('category_id')
                    ->relationship('category', 'name')
                        ->searchable()
                        ->required()
                    ->filled(),
                    Textarea::make('description')
                    ->label('Product description')
                    ->filled(),
                    Textinput::make('short_description')
                    ->label('short description')
                    ->filled(),
                    Textinput::make('sku')
                    ->label('Product SKU')
                    ->filled(),
                     Textinput::make('slug')
                    ->label('Product slug')
                    ->filled(),
                    Textinput::make('base_price')
                    ->label('Product base price')
                    ->inputMode('decimal')
                    ->filled(),
                    Textinput::make('sale_price')
                    ->label('Product Sale price')
                    ->inputMode('decimal')
                    ->filled(),
                    Textinput::make('cost_price')
                    ->label('Product Cost price')
                    ->inputMode('decimal')
                    ->filled(),
                    Select::make('is_active')
                    ->label('Active Status')
                        ->options([
                            0 => 'Inactive',
                            1 => 'Active',
                        ]),
                    Select::make('is_featured')
                    ->label('Active Status')
                        ->options([
                            0 => 'Not featured',
                            1 => 'Featured',
                        ]),

                    Textinput::make('Image path')
                    ->label('Image path (optional)'),
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