<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Conversion ' . $conversion->created_at ) }}
        </h2>
        <x-link href="{{ url('conversions') }}">Your conversions</x-link>
    </x-slot>

    <div class="sm:px-0 pt-0 space-y-6">
        <div class="px-4 sm:rounded-lg">
            <x-link href="{{ url('downloadBibtex/' . $conversion->id) }}">Download BibTeX file</x-link>
            &nbsp;&bull;&nbsp;
            <x-link href="{{ url('downloadSource/' . $conversion->user_file_id) }}">source file</x-link>
        </div>
    </div>

    <div class="sm:px-0 pt-2 space-y-6">
        <div class="px-4 sm:rounded-lg">
            <div class="mt-0">
                <p>
                    {{ count($convertedItems) }} {{ Str::plural('item', $convertedItems) }} converted
                </p>
            </div>
            <div class="ml-0">
                Settings:
                sep = {{ $conversion->item_separator}}
                &nbsp;&bull;&nbsp;
                labels = {{ $conversion->label_style }}
                &nbsp;&bull;&nbsp;
                line endings = {{ $conversion->line_endings }}
                &nbsp;&bull;&nbsp;
                {{ $conversion->char_encoding }}
                &nbsp;&bull;&nbsp;
                {{ $conversion->percent_comment ? '% = comment' : '% != comment' }}
                &nbsp;&bull;&nbsp;
                {{ $conversion->include_source ? 'include source' : 'no source' }}
                &nbsp;&bull;&nbsp;
                {{ $conversion->report_type }} report
            </div>

            <ul>
            @foreach ($convertedItems as $outputId => $convertedItem)
                <div class="mt-4">
                    <li>
                        {{ $convertedItem['source'] }}
                        <br/>

                        <div>
                            <livewire:show-converted-item 
                                    :convertedItem="$convertedItem" 
                                    :itemTypes="$itemTypes" 
                                    :outputId="$outputId" 
                                    :itemTypeOptions="$itemTypeOptions"
                            />
                        </div>
                    </li>
                </div>
            @endforeach
        </div>
    </div>

</x-app-layout>
