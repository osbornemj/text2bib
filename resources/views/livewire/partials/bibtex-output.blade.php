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
                </p>
            </div>
            <ul>
                @foreach ($convertedItems as $outputId => $convertedItem)
                <div class="mt-4">
                    <li>
                        @if (Auth::user()->is_admin)
                            <x-link href="{{ url('admin/formatExample/' . $outputId)}}" target="_blank">Format result for Examples Seeder</x-link>
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


