<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

class ProductInfo
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make([
                    'default' => 1,
                    'lg' => 3,
                ])->schema([
                    Section::make('Product Overview')
                        ->schema([
                            Grid::make([
                                'default' => 1,
                                'md' => 3,
                            ])->schema([
                                ImageEntry::make('image_path')
                                    ->label('Image')
                                    ->disk('public')
                                    ->visibility('public')
                                    ->maxWidth(600)
                                    ->columnSpan([
                                        'default' => 1,
                                        'md' => 1,
                                    ]),
                                Group::make([
                                    TextEntry::make('name')
                                        ->label('Product')
                                        ->weight(FontWeight::Bold)
                                        ->size(TextSize::Large),
                                    Grid::make([
                                        'default' => 1,
                                        'md' => 2,
                                    ])->schema([
                                        TextEntry::make('sku')
                                            ->label('SKU')
                                            ->copyable()
                                            ->copyMessage('SKU copied'),
                                        TextEntry::make('category.name')
                                            ->label('Category'),
                                    ]),
                                    Grid::make([
                                        'default' => 1,
                                        'md' => 3,
                                    ])->schema([
                                        TextEntry::make('stock_status')
                                            ->label('Stock Status')
                                            ->badge()
                                            ->formatStateUsing(fn(string $state): string => str($state)->headline())
                                            ->color(fn(string $state): string => match ($state) {
                                                'available' => 'success',
                                                'preorder' => 'info',
                                                'unavailable' => 'danger',
                                                default => 'gray',
                                            }),
                                        TextEntry::make('is_active')
                                            ->label('Live in Store')
                                            ->badge()
                                            ->formatStateUsing(fn(?bool $state): string => $state ? 'Active' : 'Inactive')
                                            ->color(fn(?bool $state): string => $state ? 'success' : 'gray'),
                                        TextEntry::make('is_featured')
                                            ->label('Featured')
                                            ->badge()
                                            ->formatStateUsing(fn(?bool $state): string => $state ? 'Featured' : 'Hidden')
                                            ->color(fn(?bool $state): string => $state ? 'warning' : 'gray'),
                                    ]),
                                ])->columnSpan([
                                    'default' => 1,
                                    'md' => 2,
                                ]),
                            ]),
                            TextEntry::make('short_description')
                                ->label('Summary')
                                ->placeholder('No short description provided.')
                                ->columnSpanFull(),
                            TextEntry::make('description')
                                ->label('Full Description')
                                ->columnSpanFull()
                                ->placeholder('No description provided.'),
                            TextEntry::make('slug')
                                ->label('Public Slug')
                                ->copyable()
                                ->copyMessage('Slug copied'),
                        ])
                        ->columnSpan([
                            'default' => 1,
                            'lg' => 2,
                        ]),
                    Section::make('Pricing & Inventory')
                        ->schema([
                            Grid::make([
                                'default' => 1,
                                'md' => 2,
                            ])->schema([
                                Group::make([
                                    TextEntry::make('base_price')
                                        ->label('Base Price')
                                        ->money('PHP'),
                                    TextEntry::make('sale_price')
                                        ->label('Sale Price')
                                        ->money('PHP')
                                        ->placeholder('—'),
                                    TextEntry::make('cost_price')
                                        ->label('Cost Price')
                                        ->money('PHP')
                                        ->placeholder('—'),
                                ])->columnSpan(1),
                                Group::make([
                                    Grid::make([
                                        'default' => 1,
                                        'md' => 2,
                                    ])->schema([
                                        TextEntry::make('stock_quantity')
                                            ->label('On Hand')
                                            ->placeholder('0'),
                                    ]),
                                ])->columnSpan(1),
                            ]),
                        ])
                        ->columnSpan([
                            'default' => 1,
                            'lg' => 1,
                        ]),
                ])->columnSpanFull(),
            ]);
    }
}
