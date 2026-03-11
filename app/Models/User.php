<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'two_factor_enabled',
        'two_factor_recovery_codes',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_enabled' => 'boolean',
            'two_factor_recovery_codes' => 'array',
            'last_login_at' => 'datetime',
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

    public function hasPurchased($bookId)
    {
        return $this->orders()
            ->whereHas('orderItems', function ($query) use ($bookId) {
                $query->where('book_id', $bookId);
            })
            ->exists();
    }

    // Two-Factor Authentication methods
    public function twoFactorSecrets()
    {
        return $this->hasMany(TwoFactorSecret::class);
    }

    public function loginLogs()
    {
        return $this->hasMany(LoginLog::class);
    }

    public function generateRecoveryCodes()
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = strtoupper(Str::random(4) . '-' . Str::random(4));
        }
        
        $this->update([
            'two_factor_recovery_codes' => $codes
        ]);
        
        return $codes;
    }

    public function useRecoveryCode($code)
    {
        $codes = $this->two_factor_recovery_codes ?? [];
        $index = array_search(strtoupper($code), $codes);
        
        if ($index !== false) {
            unset($codes[$index]);
            $this->update([
                'two_factor_recovery_codes' => array_values($codes)
            ]);
            return true;
        }
        
        return false;
    }

    public function updateLoginInfo()
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
        ]);
    }

}