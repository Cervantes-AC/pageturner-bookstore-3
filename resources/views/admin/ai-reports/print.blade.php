<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ $report->title }} - PageTurner Bookstore</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Calibri', 'Segoe UI', Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #1a1a1a;
            padding: 0.75in 0.6in;
            max-width: 100%;
        }
        .report-header {
            border-bottom: 2px solid #2d3748;
            padding-bottom: 20px;
            margin-bottom: 24px;
        }
        .report-header .subtitle {
            font-size: 10pt;
            color: #b8860b;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 4px;
        }
        .report-header h1 {
            font-size: 18pt;
            font-weight: 700;
            color: #1a202c;
        }
        .report-header .meta {
            font-size: 9pt;
            color: #718096;
            margin-top: 6px;
        }
        .report-header .meta span { margin-right: 16px; }
        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .section-heading {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
        }
        .section-heading .num {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            border-radius: 4px;
            font-size: 10pt;
            font-weight: 700;
            color: #fff;
        }
        .section-heading .num-1 { background: #b8860b; }
        .section-heading .num-2 { background: #3182ce; }
        .section-heading .num-3 { background: #805ad5; }
        .section-heading .num-4 { background: #319795; }
        .section-heading .num-5 { background: #d69e2e; }
        .section-heading .num-6 { background: #667eea; }
        .section-heading h2 {
            font-size: 13pt;
            font-weight: 600;
            color: #1a202c;
        }
        .section p, .section div.content {
            font-size: 11pt;
            color: #2d3748;
            text-align: justify;
        }
        .finding-card {
            border-left: 3px solid;
            padding: 10px 14px;
            margin-bottom: 10px;
            border-radius: 0 4px 4px 0;
        }
        .finding-card.positive { border-color: #38a169; background: #f0fff4; }
        .finding-card.warning { border-color: #d69e2e; background: #fffff0; }
        .finding-card.critical { border-color: #e53e3e; background: #fff5f5; }
        .finding-card.info { border-color: #3182ce; background: #ebf8ff; }
        .finding-card h4 { font-size: 11pt; font-weight: 600; margin-bottom: 4px; color: #1a202c; }
        .finding-card p { font-size: 10pt; }
        .finding-card .status-badge {
            display: inline-block;
            font-size: 8pt;
            font-weight: 600;
            padding: 1px 6px;
            border-radius: 3px;
            margin-top: 4px;
        }
        .rec-card {
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 10px 14px;
            margin-bottom: 8px;
        }
        .rec-card .priority-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            font-size: 8pt;
            font-weight: 700;
            color: #fff;
            float: left;
            margin-right: 8px;
        }
        .rec-card .priority-high { background: #e53e3e; }
        .rec-card .priority-medium { background: #d69e2e; }
        .rec-card .priority-low { background: #3182ce; }
        .rec-card .action { font-weight: 600; font-size: 10pt; }
        .rec-card .rationale { font-size: 9pt; color: #718096; margin-top: 2px; }
        .rec-card .priority-label {
            display: inline-block;
            font-size: 8pt;
            font-weight: 600;
            padding: 1px 6px;
            border-radius: 3px;
            margin-top: 4px;
        }
        .metadata {
            margin-top: 30px;
            padding-top: 16px;
            border-top: 1px solid #e2e8f0;
            font-size: 9pt;
            color: #718096;
        }
        .metadata table { width: 100%; border-collapse: collapse; }
        .metadata td { padding: 3px 12px 3px 0; vertical-align: top; }
        .metadata td:first-child { font-weight: 600; color: #4a5568; width: 120px; }
        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
            @page { margin: 0.75in; }
        }
    </style>
</head>
<body>

<div class="report-header">
    <div class="subtitle">PageTurner Bookstore &mdash; AI-Generated Report</div>
    <h1>{{ $report->title }}</h1>
    <div class="meta">
        <span>Generated: {{ $report->completed_at?->format('F j, Y') }}</span>
        <span>Provider: {{ ucfirst($report->provider_used ?? 'N/A') }}</span>
        <span>Model: {{ $report->model_used ?? 'N/A' }}</span>
    </div>
</div>

@php
    $introduction = $report->data['_introduction'] ?? null;
    $conclusion = $report->data['_conclusion'] ?? null;
@endphp

@if($report->summary)
    <div class="section">
        <div class="section-heading">
            <span class="num num-1">1</span>
            <h2>Executive Summary</h2>
        </div>
        <p>{{ $report->summary }}</p>
    </div>
@endif

@if($introduction)
    <div class="section">
        <div class="section-heading">
            <span class="num num-2">2</span>
            <h2>Introduction</h2>
        </div>
        <p>{{ $introduction }}</p>
    </div>
@endif

@if(!empty($report->insights))
    <div class="section">
        <div class="section-heading">
            <span class="num num-3">3</span>
            <h2>Key Findings</h2>
        </div>
        @foreach($report->insights as $finding)
            @php
                $status = $finding['status'] ?? 'info';
            @endphp
            <div class="finding-card {{ $status === 'positive' ? 'positive' : ($status === 'warning' ? 'warning' : ($status === 'critical' ? 'critical' : 'info')) }}">
                @if(!empty($finding['section'] ?? $finding['finding'] ?? ''))
                    <h4>{{ $finding['section'] ?? $finding['finding'] ?? '' }}</h4>
                @endif
                <p>{{ $finding['content'] ?? $finding['finding'] ?? '' }}</p>
                <span class="status-badge" style="background: {{ $status === 'positive' ? '#c6f6d5' : ($status === 'warning' ? '#fefcbf' : ($status === 'critical' ? '#fed7d7' : '#bee3f8')) }}; color: {{ $status === 'positive' ? '#22543d' : ($status === 'warning' ? '#744210' : ($status === 'critical' ? '#742a2a' : '#2a4365')) }}">
                    {{ ucfirst($status) }}
                </span>
            </div>
        @endforeach
    </div>
@endif

@if(!empty($report->recommendations))
    <div class="section">
        <div class="section-heading">
            <span class="num num-5">5</span>
            <h2>Recommendations</h2>
        </div>
        @foreach($report->recommendations as $rec)
            @php
                $priority = $rec['priority'] ?? 'medium';
            @endphp
            <div class="rec-card">
                <div class="priority-badge priority-{{ $priority }}">{{ strtoupper(substr($priority, 0, 1)) }}</div>
                <div class="action">{{ $rec['action'] ?? $rec['recommendation'] ?? '' }}</div>
                @if(!empty($rec['rationale']))
                    <div class="rationale">{{ $rec['rationale'] }}</div>
                @endif
                <span class="priority-label" style="background: {{ $priority === 'high' ? '#fed7d7' : ($priority === 'medium' ? '#fefcbf' : '#bee3f8') }}; color: {{ $priority === 'high' ? '#742a2a' : ($priority === 'medium' ? '#744210' : '#2a4365') }}">
                    {{ ucfirst($priority) }} Priority
                </span>
            </div>
        @endforeach
    </div>
@endif

@if($conclusion)
    <div class="section">
        <div class="section-heading">
            <span class="num num-6">6</span>
            <h2>Conclusion</h2>
        </div>
        <p>{{ $conclusion }}</p>
    </div>
@endif

<div class="metadata">
    <table>
        <tr><td>Status</td><td>{{ ucfirst($report->status) }}</td></tr>
        <tr><td>AI Provider</td><td>{{ ucfirst($report->provider_used ?? 'N/A') }}</td></tr>
        <tr><td>AI Model</td><td>{{ $report->model_used ?? 'N/A' }}</td></tr>
        <tr><td>Tokens Used</td><td>{{ $report->tokens_used ? number_format($report->tokens_used) : 'N/A' }}</td></tr>
        <tr><td>Generated By</td><td>{{ $report->user->name ?? 'System' }}</td></tr>
        <tr><td>Query</td><td>{{ $report->query }}</td></tr>
    </table>
</div>

</body>
</html>
