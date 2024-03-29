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
                <p class="mt-2">
                    <i>Settings</i>: item separator = {{ $conversion->item_separator}}
                    &nbsp;&bull;&nbsp;
                    language = {{ $conversion->language }}
                    &nbsp;&bull;&nbsp;
                    labels = {{ $conversion->label_style }}
                    &nbsp;&bull;&nbsp;
                    line endings = {{ $conversion->line_endings }}
                    <br/>
                    @if ($conversion->char_encoding == 'utf8')
                        convert accents to TeX
                    @else
                        do not convert accents to TeX
                    @endif
                    &nbsp;&bull;&nbsp;
                    {{ $conversion->percent_comment ? '% = comment' : '% != comment' }}
                    &nbsp;&bull;&nbsp;
                    {{ $conversion->include_source ? 'include source' : 'no source' }}
                    &nbsp;&bull;&nbsp;
                    {{ $conversion->report_type }} report
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


