<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Conversion ' . $conversion->created_at ) }}
        </h2>
        <x-link href="{{ url('conversions') }}">Your conversions</x-link>
    </x-slot>

    @if ($redirected)
        <span class="px-4 mt-2 text-emerald-700 dark:text-emerald-600">
            You have previously converted the file you uploaded.  Here are the results of the conversion.
        </span>
    @endif

    <div class="sm:px-0 pt-0 mt-2 space-y-6">
        <div class="px-4 sm:rounded-lg">
            <x-link href="{{ url('downloadBibtex/' . $conversion->id) }}">Download BibTeX file</x-link>
            @if ($fileExists)
                &nbsp;&bull;&nbsp;
                <x-link href="{{ url('downloadSource/' . $conversion->user_file_id) }}">source file</x-link>
            @endif
        </div>
    </div>

    <div class="sm:px-0 pt-2 space-y-6">
        <div class="px-4 sm:rounded-lg">
            <div class="mt-0">
                <p>
                    {{ count($convertedItems) }} {{ Str::plural('item', $convertedItems) }} converted
                    @if ($conversion->version)
                        &nbsp;&bull;&nbsp;
                        algorithm version {{ $conversion->version }}
                    @endif
                </p>
            </div>
            @if ($conversion->non_utf8_detected)
                <div class="ml-0 mt-2 text-red-600 dark:text-red-400">
                    The character encoding in {{ $convertedEncodingCount }} {{ Str::plural('item', $convertedEncodingCount) }} in the file you uploaded is ISO-8859-1 or Windows-1252, not UTF-8 (as indicated in red in the following list).  The script has attempted to convert {{ $convertedEncodingCount > 1 ? 'these items' : 'this item' }} to UTF-8, but may not have succeeded.  You may get better results by using <x-link href="https://notepad-plus-plus.org/" target="_blank">Notepad++</x-link> to convert your file to UTF-8 before you upload it. (Within Notepad++, click on "Encoding", and then  on "Convert to UTF-8".)
                </div>
            @endif
            <div class="ml-0 mt-2">
                @include('index.partials.settings')
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
