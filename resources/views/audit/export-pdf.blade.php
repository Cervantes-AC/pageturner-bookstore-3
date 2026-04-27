<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Audit Log Export</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1f2937; }
        h1 { font-size: 16px; margin-bottom: 4px; }
        p.meta { font-size: 9px; color: #6b7280; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        thead { background-color: #f3f4f6; }
        th { padding: 6px 8px; text-align: left; font-weight: 600; border-bottom: 2px solid #d1d5db; }
        td { padding: 5px 8px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
        tr:nth-child(even) { background-color: #f9fafb; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 9999px; font-size: 9px; font-weight: 600; }
        .badge-created  { background: #d1fae5; color: #065f46; }
        .badge-updated  { background: #dbeafe; color: #1e40af; }
        .badge-deleted  { background: #fee2e2; color: #991b1b; }
        .badge-restored { background: #fef3c7; color: #92400e; }
        .mono { font-family: monospace; font-size: 9px; }
    </style>
</head>
<body>
    <h1>PageTurner — Audit Log Export</h1>
    <p class="meta">
        Generated: {{ now()->format('M d, Y H:i:s') }}
        @if(!empty($filters['date_from']) || !empty($filters['date_to']))
            &nbsp;|&nbsp; Period: {{ $filters['date_from'] ?? '—' }} to {{ $filters['date_to'] ?? '—' }}
        @endif
        &nbsp;|&nbsp; Total records: {{ $logs->count() }}
    </p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Event</th>
                <th>Model</th>
                <th>User</th>
                <th>Changed Fields</th>
                <th>IP Address</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
            <tr>
                <td>{{ $log->id }}</td>
                <td>
                    <span class="badge badge-{{ $log->event }}">{{ ucfirst($log->event) }}</span>
                </td>
                <td>{{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}</td>
                <td>{{ $log->user?->name ?? 'System' }}</td>
                <td class="mono">
                    @if($log->new_values)
                        @foreach(collect($log->new_values)->except(['created_at','updated_at','deleted_at']) as $k => $v)
                            <div><strong>{{ $k }}</strong>: {{ is_array($v) ? json_encode($v) : $v }}</div>
                        @endforeach
                    @else
                        —
                    @endif
                </td>
                <td class="mono">{{ $log->ip_address }}</td>
                <td>{{ $log->created_at?->format('Y-m-d H:i') }}</td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center;padding:20px;color:#9ca3af;">No audit logs found</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
