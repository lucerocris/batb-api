<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                        ->label('Category Name')
                                    ->filled(),
                
                TextInput::make('slug')
                        ->label('Category Slug')
                                    ->filled(),

                Textarea::make('description')
                        ->label('Description')
                                    ->filled(),

                TextInput::make('image_path')
                    ->label('image Path')
                                ->filled(),
                Select::make('is_active')
                ->label('Active Status')
                    ->options([
                        0 => 'Inactive',
                        1 => 'Active',
                    ])
                    ->filled(),
                
            ]);
    }
}

/*

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image_url',
        'is_active',
        'image_path'
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
        'meta_data' => 'array',
    ];



*/