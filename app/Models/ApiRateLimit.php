<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiRateLimit extends Model
{
    protected $fillable = [
        'key',
        'endpoint',
        'method',
        'tier',
        'hit_count',
        'window_start',
        'user_id',
        'ip_address',
        'attempts',
        'limit',
        'throttled',
        'user_agent',
    ];

    protected $casts = [
        'window_start' => 'datetime',
        'throttled'    => 'boolean',
        'attempts'     => 'integer',
        'limit'        => 'integer',
        'hit_count'    => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
