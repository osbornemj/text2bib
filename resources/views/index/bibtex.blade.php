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
                @foreach ($outputs as $output)
                <div class="mt-4">
                    <li>
                        @if ($includeSource) 
                            % {{ $output->source }}
                            <br/>
                        @endif
                        
                        <div>
                            <livewire:report-error :output="$output" :outputId="$output->id" :itemTypeOptions="$itemTypeOptions" :fields="$fields" :itemTypeId="$itemTypeId" />
                        </div>

                    </li>
                    @endforeach
                </div>
            </ul>
        </div>
    </div>

</x-app-layout>

