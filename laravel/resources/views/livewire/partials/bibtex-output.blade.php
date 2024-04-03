<div>
    <h2 class="font-semibold text-xl leading-tight my-4">
        {{ __('File conversion report') }}
    </h2>

    <div class="sm:px-4 lg:px-0 space-y-6 pb-6">
        <div class="sm:p-0 pt-0 sm:pt-0">
            <p>
                <x-link href="{{ url('downloadBibtex/' . $conversionId) }}">Download BibTeX file</x-link>
            </p>
            <div class="mt-4">
                <p>
                    {{ count($convertedItems) }} {{ Str::plural('item', $convertedItems) }} converted
                    @if ($version)
                        &nbsp;&bull;&nbsp;
                        algorithm version {{ $version }}
                    @endif
                </p>
                @if ($notUtf8)
                    <p class="mt-2 text-red-600 dark:text-red-400">
                        The character encoding in the file you uploaded is not UTF-8.  The script has attempted to convert it to UTF-8, but may not have succeeded.  You may get better results by using <x-link href="https://notepad-plus-plus.org/" target="_blank">Notepad++</x-link> to convert it. (Within Notepad++, click on "Encoding", and then  on "Convert to UTF-8".)
                    </p>
                @endif
                <p class="mt-2 text-emerald-700 dark:text-emerald-600">
                    You can help me improve the algorithm by pointing out items that meet the requirements but nevertheless are converted incorrectly.  Submit either an error report or a comment (and please reply to any request for clarification).
                </p>
                <p class="mt-2">
                    @include('index.partials.settings')
                </p>
            </div>
            <ul>
                @foreach ($convertedItems as $outputId => $convertedItem)
                <div class="mt-4">
                    <li>
                        {{ $convertedItem['source'] }}
                        <br/>

                        @if ($reportType == 'detailed') 
                        <ul>
                            @foreach ($convertedItem['details'] as $details)
                                <li>
                                    @include('index.partials.conversionDetails')
                                </li>
                            @endforeach
                        </ul>
                        @endif

                        <div>
                            <livewire:show-converted-item 
                                :convertedItem="$convertedItem" 
                                :itemTypes="$itemTypes" 
                                :outputId="$outputId" 
                                :itemTypeOptions="$itemTypeOptions"
                            />
                        </div>
                        
                    </li>
                    @endforeach
                </div>
            </ul>
        </div>
    </div>
</div>


