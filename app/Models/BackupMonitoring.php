<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackupMonitoring extends Model
{
    protected $table = 'backup_monitoring';

    protected $fillable = [
        'name',
        'status',
        'file_path',
        'size_bytes',
        'disk',
        'output',
    ];
}
