<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Bibtex file') }}
        </h2>
    </x-slot>

    <div class="sm:px-4 lg:px-4 space-y-6">
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
                        <x-link href="https://scholar.google.com/scholar?as_q={{ $convertedItem['scholarTitle'] }}&num=100&btnG=Search+Scholar&as_sdt=1.&as_sdtp=on&as_sdtf=&as_sdts=5&hl=en" target="_blank">Google Scholar</x-link>
                        &nbsp;&bull;&nbsp;
                        <x-link href="https://www.jstor.org/action/doAdvancedSearch?q0={{ $convertedItem['scholarTitle'] }}&f0=ti&c1=AND&q1=&f1=ti&wc=on&Search=Search&sd=&ed=&la=&jo=')" target="_blank">JSTOR</x-link>
                        [new tab/window]
                        <br/>

                        @if ($includeSource) 
                            % {{ $convertedItem['source'] }}
                            <br/>
                        @endif

                        @if (count($convertedItem['warnings']))
                        <ul>
                            @foreach ($convertedItem['warnings'] as $warning)
                                <li>
                                    <span class="text-red-600">{{ $warning }}</span>
                                </li>
                            @endforeach
                        </ul>
                        @endif
                        
                        @if (count($convertedItem['notices']))
                        <ul>
                            @foreach ($convertedItem['notices'] as $notice)
                                <li>
                                    <span class="text-orange-600">{{ $notice }}</span>
                                </li>
                            @endforeach
                        </ul>
                        @endif
                        
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

</x-app-layout>

