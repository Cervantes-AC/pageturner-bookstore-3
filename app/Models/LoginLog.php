<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email',
        'ip_address',
        'user_agent',
        'successful',
        'failure_reason',
    ];

    protected $casts = [
        'successful' => 'boolean',
    ];

    /**
     * Get the user that owns the login log.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log a login attempt.
     */
    public static function logAttempt($email, $successful = false, $user = null, $failureReason = null)
    {
        return static::create([
            'user_id' => $user ? $user->id : null,
            'email' => $email,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'successful' => $successful,
            'failure_reason' => $failureReason,
        ]);
    }
}