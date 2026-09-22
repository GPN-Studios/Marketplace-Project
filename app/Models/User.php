<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_picture',
        'balance',
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
            'password' => 'hashed',
        ];
    }

    /** Saldo disponível, em centavos, formatado como "129,90". */
    protected function balanceFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => format_money($this->balance),
        );
    }

    // Relations

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function cart()
    {
        return $this->hasOne(Order::class)->where('status', OrderStatus::Cart);
    }

    public function ratingsReceived()
    {
        return $this->hasMany(Rating::class, 'seller_id');
    }

    // Avaliações que o usuário fez como comprador
    public function ratingsGiven()
    {
        return $this->hasMany(Rating::class, 'buyer_id');
    }

    // Reputação (calculada a partir das avaliações recebidas como vendedor)

    public function positiveRatingsCount(): int
    {
        return $this->ratingsReceived()->where('is_positive', true)->count();
    }

    public function negativeRatingsCount(): int
    {
        return $this->ratingsReceived()->where('is_positive', false)->count();
    }
}
