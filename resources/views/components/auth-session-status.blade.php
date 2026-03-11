@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-green-400 bg-green-900/20 border border-green-800/50 rounded-lg p-3']) }}>
        {{ $status }}
    </div>
@endif
