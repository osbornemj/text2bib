@if (is_array($details))
@foreach ($details as $key => $value)
    @switch ($key)
        @case ('fieldName')
            <span class="text-blue-500">{{ $value }}</span>
            @break
        @case ('content')
            {{ $value }}
            @break
        @case ('text')
            {{ $value }}
            @break
        @case ('words')
            @foreach ($value as $word)
                <span class="text-blue-700 bg-slate-400">{{ $word }}</span>
            @endforeach
            @break
        @case ('item')
            <span class="text-blue-300">{{ $value }}</span>
            @break
        @case ('label')
            <span class="text-teal-500">{{ $value }}</span>
            @break
        @case ('warning')
            <span class="text-red-500">{{ $value }}</span>
            @break
        @case ('notice')
            <span class="text-orange-500">{{ $value }}</span>
            @break
    @endswitch
@endforeach
@else
{{ $details }}
@endif
