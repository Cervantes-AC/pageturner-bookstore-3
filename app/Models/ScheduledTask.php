<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduledTask extends Model
{
    protected $table = 'scheduled_tasks';

    protected $fillable = [
        'command',
        'description',
        'frequency',
        'status',
        'last_run_at',
        'next_run_at',
        'last_output',
        'last_status',
    ];

    protected $casts = [
        'last_run_at' => 'datetime',
        'next_run_at' => 'datetime',
    ];
}
