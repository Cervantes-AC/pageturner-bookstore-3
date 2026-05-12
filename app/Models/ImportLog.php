<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportLog extends Model
{
    protected $fillable = [
        'user_id',
        'filename',
        'type',
        'total_rows',
        'processed_rows',
        'failed_rows',
        'failures',
        'status',
        'file_path',
    ];

    protected $casts = [
        'failures' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
