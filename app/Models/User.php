<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser, JWTSubject
{
    use HasFactory, Notifiable, SoftDeletes, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

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
        'image_path',
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

    // JWT
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    public function getNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}") ?: 'Unknown User';
    }

    // Relations
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function ordersVerified(): HasMany
    {
        return $this->hasMany(Order::class, 'payment_verified_by');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class, 'user_id');
    }

    public function inventoryLogs(): HasMany
    {
        return $this->hasMany(InventoryMovement::class, 'user_id');
    }

    // Accessors
    public function getTotalOrdersAttribute(): int
    {
        return $this->orders()->count();
    }

    public function getTotalSpentAttribute(): float
    {
        return $this->orders()->sum('total_amount');
    }

    // Name accessor for Filament compatibility
    public function getNameAttribute(): string
    {
        if ($this->first_name && $this->last_name) {
            return trim("{$this->first_name} {$this->last_name}");
        }

        if ($this->first_name) {
            return $this->first_name;
        }

        if ($this->username) {
            return $this->username;
        }

        return $this->email ?? 'User';
    }

    // Filament: Display Name
    public function getFilamentName(): string
    {
        return $this->name;
    }

    // REQUIRED FOR FILAMENT v3
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === 'admin';
    }
}
