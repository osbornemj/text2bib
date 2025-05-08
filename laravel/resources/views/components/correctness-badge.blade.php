@props([
    'count',
    'class' => 'bg-slate-300 dark:bg-slate-500', // default color
])

@if ($count)
    <span {{ $attributes->merge(['class' => "$class text-xs px-1"]) }}>
        {{ $count }}
    </span>
@endif
