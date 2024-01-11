<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Result of example check
        </h2>
        <x-link :href="route('examples.index')" :active="request()->routeIs('examples.index')">Examples</x-link>
    </x-slot>

    <div class="sm:px-0 lg:px-0 space-y-6">
        <div class="px-4 sm:px-4 pt-0 sm:pt-0 sm:rounded-lg">
            @if ($allCorrect) 
                <span class="bg-green-600">All correct</span>
            @else
                @foreach ($results as $id => $result)
                    <p>Example {{ $id }}
                    <span class="bg-red-600">incorrect</span> 
                    &nbsp;&bull;&nbsp;
                    <x-link href="{{ url('/admin/runExampleCheck/1/' . $id) }}">verbose conversion</x-link>
                    &nbsp;&bull;&nbsp;
                    <x-link href="{{ url('/admin/runExampleCheck/0/' . $id) }}">brief conversion</x-link>
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
            @endif
        </div>
    </div>
</x-app-layout>
