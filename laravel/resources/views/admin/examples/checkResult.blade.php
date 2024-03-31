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
                        <div class="mt-2">
                            {{ $result['source']}}
                        </div>
                        <div class="mt-2">
                            @php
                                $selectedLanguage = [];
                                $selectedLanguage[$result['language']] = true;
                                $selectedCharEncoding = [];
                                $selectedCharEncoding[$result['charEncoding']] = true;
                            @endphp
                            <form method="POST" action="{{ url('/admin/runExampleCheck') }}" class="mt-0 space-y-0">
                                @csrf
                                <input type="hidden" id="exampleId" name="exampleId" value="{{ $id }}"/>
                                <x-select-input id="report_type" name="report_type" :options="$typeOptions" class="p-2"></x-select-input-plain>
                                <x-select-input id="char_encoding" name="char_encoding" :selected="$selectedCharEncoding" :options="$utf8Options" class="p-2"></x-select-input-plain>
                                <x-select-input id="language" name="language" :options="$languageOptions" :selected="$selectedLanguage" class="p-2"></x-select-input-plain>
                                <x-select-input id="detailsIfCorrect" name="detailsIfCorrect" :options="$detailOptions" class="p-2"></x-select-input-plain>
                                <x-primary-button class="ml-0">
                                    {{ __('Submit') }}
                                </x-primary-button>
                            </form>
                        </div>
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
