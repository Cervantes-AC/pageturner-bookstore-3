<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TwoFactorSecret extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'code',
        'expires_at',
        'used',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used' => 'boolean',
    ];

    /**
     * Get the user that owns the two factor secret.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the code is expired.
     */
    public function isExpired()
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if the code is valid (not used and not expired).
     */
    public function isValid()
    {
        return !$this->used && !$this->isExpired();
    }

    /**
     * Mark the code as used.
     */
    public function markAsUsed()
    {
        $this->update(['used' => true]);
    }

    /**
     * Generate a new 2FA code for a user.
     */
    public static function generateForUser(User $user)
    {
        // Delete any existing unused codes
        static::where('user_id', $user->id)
            ->where('used', false)
            ->delete();

        // Generate new code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        return static::create([
            'user_id' => $user->id,
            'code' => $code,
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);
    }
}