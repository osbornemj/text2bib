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
                                    @if (is_array($details))
                                        @foreach ($details as $key => $value)
                                            @switch ($key)
                                                @case ('fieldName')
                                                    <span class="text-blue-500">{{ $value }}</span>
                                                    @break
                                                @case ('content')
                                                    {{ $value }}
                                                    @break
                                                @case ('text')
                                                    {{ $value }}
                                                    @break
                                                @case ('words')
                                                    @foreach ($value as $word)
                                                        <span class="text-blue-700 bg-slate-400">{{ $word }}</span>
                                                    @endforeach
                                                    @break
                                                @case ('item')
                                                    <span class="text-blue-300">{{ $value }}</span>
                                                    @break
                                                @case ('label')
                                                    <span class="text-teal-500">{{ $value }}</span>
                                                    @break
                                                @case ('warning')
                                                    <span class="text-red-500">{{ $value }}</span>
                                                    @break
                                                @case ('notice')
                                                    <span class="text-orange-500">{{ $value }}</span>
                                                    @break
                                            @endswitch
                                        @endforeach
                                    @else
                                        {{ $details }}
                                    @endif
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

