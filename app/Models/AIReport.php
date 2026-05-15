<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AIReport extends Model
{
    use SoftDeletes;

    protected $table = 'ai_reports';

    protected $fillable = [
        'user_id',
        'title',
        'query',
        'summary',
        'data',
        'insights',
        'recommendations',
        'ai_prompt',
        'ai_raw_response',
        'provider_used',
        'model_used',
        'tokens_used',
        'status',
        'error_message',
        'completed_at',
    ];

    protected $casts = [
        'data' => 'array',
        'insights' => 'array',
        'recommendations' => 'array',
        'tokens_used' => 'integer',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
