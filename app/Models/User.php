<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_enabled' => 'boolean',
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function getRateLimitTier(): string
    {
        if ($this->isAdmin()) return 'admin';
        if ($this->role === 'premium') return 'premium';
        return 'standard';
    }

    public function hasPurchased($bookId)
    {
        return $this->orders()
            ->whereHas('orderItems', function ($query) use ($bookId) {
                $query->where('book_id', $bookId);
            })
            ->exists();
    }

    public function twoFactorRecoveryCodes(): array
    {
        if (empty($this->two_factor_recovery_codes)) {
            return [];
        }
        return json_decode(decrypt($this->two_factor_recovery_codes), true) ?? [];
    }

    public function setTwoFactorRecoveryCodes(array $codes): void
    {
        $this->two_factor_recovery_codes = encrypt(json_encode($codes));
    }

    public function generateRecoveryCode(): string
    {
        return strtoupper(substr(md5(uniqid()), 0, 10));
    }
}
