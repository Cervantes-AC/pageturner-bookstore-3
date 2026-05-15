<?php

namespace App\Exports;

use App\Models\AuditLog;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class AuditLogExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading
{
    protected $filters;
    protected $userCache = [];

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
        // Pre-load all users into memory once
        $this->userCache = User::pluck('name', 'id')->toArray();
    }

    public function query()
    {
        $query = AuditLog::select(['id', 'user_id', 'event', 'auditable_type', 'auditable_id', 'ip_address', 'url', 'method', 'created_at']);

        if (!empty($this->filters['user_id'])) {
            $query->where('user_id', $this->filters['user_id']);
        }
        if (!empty($this->filters['event'])) {
            $query->where('event', $this->filters['event']);
        }
        if (!empty($this->filters['auditable_type'])) {
            $query->where('auditable_type', $this->filters['auditable_type']);
        }
        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }
        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        $query->orderBy('created_at', 'desc');
        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'User',
            'Event',
            'Auditable Type',
            'Auditable ID',
            'IP Address',
            'URL',
            'Method',
            'Created At',
        ];
    }

    public function map($log): array
    {
        return [
            $log->id,
            $this->userCache[$log->user_id] ?? 'System',
            $log->event,
            class_basename($log->auditable_type),
            $log->auditable_id,
            $log->ip_address,
            $log->url,
            $log->method,
            $log->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function chunkSize(): int
    {
        return 250;
    }
}
