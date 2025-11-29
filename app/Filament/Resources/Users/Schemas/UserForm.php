<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;

use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
class UserForm
{
    public static function configure(Schema $schema): Schema
    {
     
        return $schema
            ->components([
                Section::make('Register user')
                ->description('used only for debugging!')
                ->schema([
                TextInput::make('first_name')
                    ->label('First Name')
                    ->filled(),
                    TextInput::make('last_name')
                    ->label('Last Name')
                    ->filled(),
                    Select::make('role')
                    ->label('User Role')
                        ->options([
                            'customer' => 'customer',
                            'admin' => 'admin',
                        ])
                    ->filled(),
                    TextInput::make('email')
                    ->label('Email')
                    ->filled()
                    ->email(),
                    Textinput::make('password')
                    ->filled()
                    ->password(),
                    Textinput::make('phone_number')
                    ->label('Phone number')
                    ->filled()
                    ->tel(),


                    
                ]),
                

            ]);
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

    /**
*/