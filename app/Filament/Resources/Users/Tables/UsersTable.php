<?php

namespace App\Filament\Resources\Users\Tables;

use App\Livewire\UserStatistcs;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Card;


class UsersTable 
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // ImageColumn::make('image_preview')
                //     ->label('Avatar')
                //     ->circular()
                //     ->height(45)
                //     ->getStateUsing(function ($record) {
                //         if ($record->image_path) {
                //             return asset('storage/' . ltrim($record->image_path, '/'));
                //         }

                //         return $record->image_url;
                //     }),
                TextColumn::make('role')->sortable(),
                TextColumn::make('first_name')->sortable(),
                TextColumn::make('last_name')->sortable(),
                TextColumn::make('email')->sortable(),
                TextColumn::make('phone_number')
                    ->default('No number'),
                TextColumn::make('total_orders')
                    ->label('Total Orders made'),

            ])
            
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
        // Card::make()->extraAttributes(['class' => 'bg-gray-50']);
    }

}



/*

    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'email',
        'email_verified_at',
        'password',
        'role',
        'phone_number',
        'date_of_birth',
        'username',
        'total_orders',
        'total_spent',
        'failed_login_attempts',
        'locked_until',
        'image_path'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'date_of_birth' => 'date',
            'total_orders' => 'integer',
            'total_spent' => 'float',
            'failed_login_attempts' => 'integer',
            'locked_until' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }



*/