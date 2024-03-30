<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Result of example check
        </h2>
        <x-link :href="route('examples.index')" :active="request()->routeIs('examples.index')">Examples</x-link>
    </x-slot>

    <div class="px-4 sm:rounded-lg">
        @if ($allCorrect && $detailsIfCorrect == 'hide') 
            <span class="bg-green-600">All correct</span> ({{ $exampleCount }} {{ Str::plural('item', $exampleCount) }})
        @else
            <div class="pb-2">
                <p>
                    @if (count($results) == 1)
                        1 conversion
                    @else
                        {{ count($results) }} incorrect {{ Str::plural('conversion', $results) }}
                    @endif
                </p>
            </div>
            @foreach ($results as $id => $result)
                <div class="pt-2 border-2 border-b-transparent border-l-transparent border-r-transparent border-t-indigo-800 dark:border-t-slate-200">
                    <p>Example {{ $id }}
                        @if ($result['result'] == 'correct')
                            <span class="dark:bg-green-600">Correct</span>
                        @else
                            <span class="bg-red-500 text-white dark:bg-red-600">incorrect</span> 
                        @endif
                    &nbsp;&bull;&nbsp;
                    <x-link href="{{ url('/admin/runExampleCheck/detailed/' . $result['language'] . '/show/' . $id) }}">verbose conversion</x-link>
                    &nbsp;&bull;&nbsp;
                    <x-link href="{{ url('/admin/runExampleCheck/brief/' . $result['language'] . '/show/' . $id) }}">brief conversion</x-link>
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
                    @if ($reportType == 'detailed')
                        <div class="mt-4">
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
</x-app-layout>
