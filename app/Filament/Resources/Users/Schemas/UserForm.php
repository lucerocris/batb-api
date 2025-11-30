<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

use Filament\Schemas\Components\Section;
class UserForm
{
    public static function configure(Schema $schema): Schema
    {
     
        return $schema
            ->components([
                TextInput::make('first_name')
                    ->label('First Name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('last_name')
                    ->label('Last Name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('username')
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('phone_number')
                    ->label('Phone Number')
                    ->tel()
                    ->maxLength(20),
                DatePicker::make('date_of_birth')
                    ->label('Date of Birth')
                    ->native(false)
                    ->maxDate(now()),
                Select::make('role')
                    ->options([
                        'admin' => 'Admin',
                        'customer' => 'Customer',
                        'manager' => 'Manager',
                    ])
                    ->required()
                    ->default('customer'),
                TextInput::make('password')
                    ->password()
                    ->minLength(8)
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create'),
                TextInput::make('password_confirmation')
                    ->label('Confirm Password')
                    ->password()
                    ->minLength(8)
                    ->dehydrated(false)
                    ->required(fn (string $context): bool => $context === 'create')
                    ->same('password'),
                FileUpload::make('image')
                    ->label('Profile Image')
                    ->image()
                    ->directory('users')
                    ->disk('public')
                    ->imageEditor()
                    ->maxSize(2048)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg'])
                    ->avatar()
                    ->columnSpanFull(),
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