<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackupMonitoring extends Model
{
    protected $table = 'backup_monitoring';

    protected $fillable = [
        'name', 'status', 'disk', 'size', 'path', 'message', 'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function getFormattedSizeAttribute(): string
    {
        if (!$this->size) return 'N/A';
        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->size;
        $i = 0;
        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }
        return round($size, 2) . ' ' . $units[$i];
    }
}
