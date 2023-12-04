<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Result of example check
        </h2>
        <x-link :href="route('examples.index')" :active="request()->routeIs('examples.index')">Examples</x-link>
    </x-slot>

    <div class="sm:px-0 lg:px-0 space-y-6">
        <div class="px-4 sm:px-4 pt-0 sm:pt-0 sm:rounded-lg">
            @foreach ($results as $id => $result)
                <p>Example {{ $id }}
                @if ($result['result'] == 'correct')
                    <span class="bg-green-600">correct</span>
                    @isset ($result['unidentified'])
                        (but string "{{ $result['unidentified'] }}" not assigned to field)
                    @endisset
                @else
                    <span class="bg-red-600">incorrect</span> &nbsp;&bull;&nbsp;
                    <a href="{{ url('/admin/runExampleCheck/1/' . $id) }}">verbose conversion</a>
                    <br/>
                    Source: {{ $result['source'] }}
                    @foreach ($result['errors'] as $key => $value)
                        <br/>
                        {{ $key }}:
                        <br/>
                        {{ $value['content'] }}
                        <br/>
                        instead of
                        <br/>
                        {{ $value['correct']}}
                    @endforeach
                @endif
                @if ($verbose)
                    <p>
                    <i>Details of conversion:</i>
                    <ul>
                    @foreach ($result['details'] as $details)
                        <li>
                            @include('index.partials.conversionDetails')
                        </li>
                    @endforeach
                    </ul>
                @endif
            @endforeach
        </div>
    </div>
</x-app-layout>
