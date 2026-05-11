<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduledTask extends Model
{
    protected $fillable = [
        'command',
        'description',
        'frequency',
        'status',
        'output',
        'duration',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'started_at'  => 'datetime',
        'finished_at' => 'datetime',
        'duration'    => 'float',
    ];

    public function scopeRecent($query)
    {
        return $query->latest()->take(20);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}
