<div>
    <h2 class="font-semibold text-xl leading-tight my-4">
        {{ __('File conversion report') }}
    </h2>

    <div class="sm:px-4 lg:px-0 space-y-6 pb-6">
        <div class="sm:p-0 pt-0 sm:pt-0">
            <p>
                <x-link href="{{ url('downloadBibtex/' . $conversionId) }}">Download BibTeX file</x-link>
            </p>
            <ul>
                @foreach ($convertedItems as $outputId => $convertedItem)
                <div class="mt-4">
                    <li>
                        @if (Auth::user()->is_admin)
                            <x-link href="{{ url('admin/formatExample/' . $outputId)}}" target="_blank">Format result for Examples Seeder</x-link>
                            <br/>
                        @endif

                        Check in
                        <x-link href="https://scholar.google.com/scholar?q={{ $convertedItem['scholarTitle'] }}&num=100&btnG=Search+Scholar&as_sdt=1.&as_sdtp=on&as_sdtf=&as_sdts=5&hl=en" target="_blank">Google Scholar</x-link>
                        &nbsp;&bull;&nbsp;
                        <x-link href="https://www.jstor.org/action/doAdvancedSearch?q0={{ $convertedItem['scholarTitle'] }}&f0=ti&c1=AND&q1=&f1=ti&wc=on&Search=Search&sd=&ed=&la=&jo=')" target="_blank">JSTOR</x-link>
                        [new tab/window]
                        <br/>

                        <i>Source</i>: {{ $convertedItem['source'] }}
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


