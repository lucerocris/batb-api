<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

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
        'image_path'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
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

    /**
     * Get the identifier that will be stored in the JWT subject claim
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key-value array containing any custom claims to be added to JWT
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function orders() : HasMany
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function ordersVerified() : HasMany
    {
        return $this->hasMany(Order::class, 'payment_verified_by');
    }

    public function addresses() : HasMany
    {
        return $this->hasMany(Address::class, 'user_id');
    }

    public function inventoryLogs() : HasMany
    {
        return $this->hasMany(InventoryMovement::class, 'user_id');
    }

    public function getTotalOrdersAttribute(): int
    {
        return $this->orders()->count();
    }

    public function getTotalSpentAttribute(): float
    {
        return $this->orders()->sum('total_amount');
    }
}
