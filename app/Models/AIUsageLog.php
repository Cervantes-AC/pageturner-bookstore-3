<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AIUsageLog extends Model
{
    protected $table = 'ai_usage_logs';

    protected $fillable = [
        'provider',
        'feature',
        'prompt_hash',
        'response_hash',
        'tokens_used',
        'cost_estimate',
        'success',
        'error_message',
        'user_id',
        'model_used',
        'response_time_ms',
    ];

    protected $casts = [
        'tokens_used' => 'integer',
        'cost_estimate' => 'decimal:6',
        'success' => 'boolean',
        'response_time_ms' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeByProvider($query, $provider)
    {
        return $query->where('provider', $provider);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('success', true);
    }

    public function scopeFailed($query)
    {
        return $query->where('success', false);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public static function totalTokensToday(): int
    {
        return static::whereDate('created_at', today())->sum('tokens_used');
    }

    public static function totalTokensThisWeek(): int
    {
        return static::where('created_at', '>=', now()->startOfWeek())->sum('tokens_used');
    }

    public static function totalCostToday(): float
    {
        return static::whereDate('created_at', today())->sum('cost_estimate');
    }
}
