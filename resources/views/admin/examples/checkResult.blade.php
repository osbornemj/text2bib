<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Result of example check
        </h2>
        <x-link :href="route('examples.index')" :active="request()->routeIs('examples.index')">Examples</x-link>
    </x-slot>

    <div class="sm:px-0 lg:px-0 space-y-6">
        <div class="px-4 sm:px-4 pt-0 sm:pt-0 sm:rounded-lg">
            @if ($allCorrect && !$showDetailsIfCorrect) 
                <span class="bg-green-600">All correct</span> ({{ $exampleCount }} {{ Str::plural('item', $exampleCount) }})
            @else
                @foreach ($results as $id => $result)
                    <hr>
                    <div class="my-4">
                        <p>Example {{ $id }}
                            @if ($result['result'] == 'correct')
                                <span class="bg-green-600">Correct</span>
                            @else
                                <span class="bg-red-600">incorrect</span> 
                            @endif
                        &nbsp;&bull;&nbsp;
                        <x-link href="{{ url('/admin/runExampleCheck/1/' . ($showDetailsIfCorrect ? '1' : '0') . '/' . $id) }}">verbose conversion</x-link>
                        &nbsp;&bull;&nbsp;
                        <x-link href="{{ url('/admin/runExampleCheck/0/' . ($showDetailsIfCorrect ? '1' : '0') . '/' . $id) }}">brief conversion</x-link>
                        <br/>
                        <i>Source</i>: {{ $result['source'] }}
                        @if (isset($result['typeError']))
                            <div class="mt-4">
                            Type <span class="text-blue-700 dark:bg-rose-300 bg-rose-400">{{ $result['typeError']['content'] }}</span> instead of <span class="text-blue-700 dark:bg-green-300 bg-green-400">{{ $result['typeError']['correct']}}</span>
                            </div>
                        @endif
                        @foreach ($result['errors'] as $key => $value)
                            <div class="mt-4">
                            <span class="text-blue-700 dark:bg-slate-200 bg-slate-400">{{ $key }}</span>
                            <br/>
                            {{ $value['content'] ?: '[null]' }}
                            <br/>
                            <i>instead of</i>
                            <br/>
                            {{ $value['correct'] ?: '[null]' }}
                            </div>
                        @endforeach
                        @if ($verbose)
                            <div class="mt-4">
                            <i>Details of conversion:</i>
                            <ul>
                            @foreach ($result['details'] as $details)
                                <li>
                                    @include('index.partials.conversionDetails')
                                </li>
                            @endforeach
                            </ul>
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</x-app-layout>
