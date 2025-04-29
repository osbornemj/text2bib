<div>
    <h2 class="font-semibold text-xl leading-tight my-4">
        {{ __('File conversion report') }}
    </h2>

    <div class="sm:px-4 lg:px-0 space-y-6 pb-6">
        <div class="sm:p-0 pt-0 sm:pt-0">
            <p>
                <x-link href="{{ url('downloadBibtex/' . $conversionId) }}">Download BibTeX file</x-link>
            </p>

            @if (count($invalidItems))
            <div class="mt-4 border-b">
                The following {{ Str::plural('item', $invalidItems) }} in your file @if (count($invalidItems) > 1) are @else is @endif invalid, and were not converted.  (Every item must contain at least one author, a title, and publication information, and cannot exceed 1,000 characters in length.)
                <ul class="my-4">
                    @foreach($invalidItems as $invalidItem)
                    <li class="ml-4 mb-2">
                        {{ $invalidItem['source'] }}
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

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
                        The character encoding in {{ $convertedEncodingCount }} {{ Str::plural('item', $convertedEncodingCount) }} in the file you uploaded is ISO-8859-1 or Windows-1252, not UTF-8 (as indicated in red in the following list).  The script has attempted to convert {{ $convertedEncodingCount > 1 ? 'these items' : 'this item' }} to UTF-8, but may not have succeeded.  You may get better results by using <x-link href="https://notepad-plus-plus.org/" target="_blank">Notepad++</x-link> to convert your file to UTF-8 before you upload it. (Within Notepad++, click on "Encoding", and then on "Convert to UTF-8".)
                    </p>
                @endif
                {{--
                <p class="mt-2 text-emerald-700 dark:text-emerald-600">
                    You can help me improve the algorithm by pointing out items that meet the requirements but nevertheless are converted incorrectly.  Submit either an error report or a comment (and please reply to any request for clarification).
                </p>
                --}}
                <p class="mt-2">
                    @include('index.partials.settings')
                </p>
            </div>

            <ul>
                @foreach ($convertedItems as $outputId => $convertedItem)
                <a name="{{ $outputId }}"></a>
                <div class="mt-4">
                    <li>
                        @if ($convertedItem['detected_encoding'] != 'UTF-8')
                        <span class="text-red-600 dark:text-red-400">
                            Detected character encoding: {{ $convertedItem['detected_encoding']}}.
                        </span>
                        <br/>
                        @endif
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

                        @php
                            $language = $conversion->language;
                        @endphp

                        <div>
                            <livewire:show-converted-item 
                                :convertedItem="$convertedItem" 
                                :itemTypes="$itemTypes" 
                                :outputId="$outputId" 
                                :itemTypeOptions="$itemTypeOptions"
                                :language="$language"
                            />
                        </div>

                    </li>
                    @endforeach
                </div>
            </ul>
        </div>
    </div>
</div>


