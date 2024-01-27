@if (is_array($details))
    @if (array_key_exists('fieldName', $details))
        <span class="text-blue-500">{{ $details['fieldName'] }}</span> {{ $details['content']}}
    @else    
        @foreach ($details as $key => $value)
            @switch ($key)
                @case ('content')
                @case ('text')
                    {{ $value }}
                    @break
                @case ('words')
                    @foreach ($value as $word)
                        <span class="text-blue-700 bg-slate-400">{{ $word }}</span>
                    @endforeach
                    @break
                @case ('addition')
                    <span class="text-green-500">{{ $value }}</span>
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
    @endif
@else
{{ $details }}
@endif
