<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use OwenIt\Auditing\Models\Audit;

class AuditLogsExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading, ShouldAutoSize
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Audit::with('user')->latest();

        if (!empty($this->filters['user_id'])) {
            $query->where('user_id', $this->filters['user_id']);
        }
        if (!empty($this->filters['event'])) {
            $query->where('event', $this->filters['event']);
        }
        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }
        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        return $query;
    }

    public function headings(): array
    {
        return ['ID', 'User', 'Event', 'Auditable Type', 'Auditable ID', 'IP Address', 'URL', 'Timestamp'];
    }

    public function map($audit): array
    {
        return [
            $audit->id,
            $audit->user?->name ?? 'System',
            $audit->event,
            class_basename($audit->auditable_type ?? ''),
            $audit->auditable_id,
            $audit->ip_address,
            $audit->url,
            $audit->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
