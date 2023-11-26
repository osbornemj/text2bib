<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Item conversion report') }}
        </h2>
    </x-slot>

    <div class="sm:px-4 lg:px-4 space-y-6">
        <div class="sm:p-0 pt-0 sm:pt-0">
            <form method="POST" action="{{ route('conversion.addOutput', ['conversionId' => $conversion->id]) }}">
                @csrf
        
                <input type="hidden" id="index" name="index" value="{{ $index }}" />
                <input type="hidden" id="entryCount" name="entryCount" value="{{ $entryCount }}" />
                <input type="hidden" id="source" name="source" value="{{ $bibtexItem['source'] }}" />
                <input type="hidden" id="kind" name="kind" value="{{ $bibtexItem['item']->kind }}" />
                
                <div>
                    {{ $index + 1 }} of {{ $entryCount }} {{ Str::plural('reference', $entryCount) }}
                </div>

                <div class="mt-4">
                    <span class="font-bold dark:text-sky-700">Source</span>
                    {{ $bibtexItem['source'] }}
                </div>

                <div class="mt-4">
                    <span class="font-bold dark:text-sky-700">BibTeX</span>
                    <ul>
                    <li>
                        <span class="ml-4 text-blue-500">Type</span> {{ $bibtexItem['item']->kind }}
                    </li>
                    <li>
                        <span class="ml-4 text-blue-500">Label</span> {{ $bibtexItem['item']->label }}
                    </li>
                    @foreach ($bibtexItem['item'] as $key => $content)
                        @if (!in_array($key, ['kind', 'label']))
                            <li><span class="px-1 ml-4 bg-blue-200 dark:text-gray-800">{{ $key }}</span> {{ $content }}</li>
                        @endif
                    @endforeach
                    @foreach ($bibtexItem['warnings'] as $content)
                        <li><span class="ml-4 text-red-500">Warning</span>: {{ $content }}</li>
                    @endforeach
                    @foreach ($bibtexItem['notices'] as $content)
                        <li><span class="ml-4 text-orange-300">Notice</span>: {{ $content }}</li>
                    @endforeach
                    </ul>
                </div>

                @if ($index + 1 < $entryCount)
                    <x-primary-button class="ml-0 mt-3">
                            {{ __('Add item to BibTeX file and continue') }}
                    </x-primary-button>
                    <x-link class="ml-3" href="{{ url('convertIncremental/' . $conversion->id . '/' . $index + 1) }}">Skip item and continue to next one</x-link>
                @else
                    <x-primary-button class="ml-0 mt-3">
                        {{ __('Add item to BibTeX file and display BibTeX file') }}
                    </x-primary-button>
                @endif

                <div class="bg-blue-100 dark:bg-sky-800">
                    <div class="mt-4 ml-4 p-4">
                        <ul class="-indent-4">
                            <li class="mb-4">
                                <b>If the conversion is correct</b>, click the 
                                <span class="px-2 py-1 bg-gray-800 dark:bg-blue-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">Add&nbsp;item&nbsp;...</span> button to add it to the bibtex file and go to the next item.
                            </li>
                            <li>
                                <b>If the conversion is not correct,</b>
                                <ul class="ml-4 -indent-4">
                                    <li class="mt-2">
                                        <i>either</i> edit the fields in the form below and then click the <span class="px-2 py-1 bg-gray-800 dark:bg-blue-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">Add&nbsp;item&nbsp;...</span> button
                                    </li>
                                    <li class="mt-2">
                                        <i>or</i> edit the source at the bottom of the page and click the <span class="px-2 py-1 bg-gray-800 dark:bg-blue-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">Resubmit</span> button to re-do the conversion.  (If the title has not been correctly identified, putting it in quotation marks will help.)
                                    </li>
                                </ul>
                            </li> 
                        </ul>
                    </div>
                </div>

                <div class="mt-4 mb-2">
                    <b>Type</b>: {{ $bibtexItem['item']->kind }}
                </div>

                @foreach ($itemFields as $field)
                    @if (!in_array($field->name, ['kind']))
                    @php
                        $name = $field->name;
                        $value = $bibtexItem['item']->{$name} ?? null;    
                    @endphp
                    <div>
                        <x-input-label :for="$name" :value="$name" />
                        <x-text-input :id="$name" class="block mt-1 w-full" type="text" :name="$name" :value="$value" />
                    </div>
                    @endif
                @endforeach
        
                @foreach ($bibtexItem['warnings'] as $content)
                    <div><span class="text-red-500">Warning</span>: {{ $content }}</div>
                @endforeach
                @foreach ($bibtexItem['notices'] as $content)
                    <div><span class="text-orange-300">Notice</span>: {{ $content }}</div>
                @endforeach

                @if ($index + 1 < $entryCount)
                    <x-primary-button class="ml-0 mt-3">
                            {{ __('Add item to BibTeX file and continue') }}
                    </x-primary-button>
                    <x-link class="ml-3" href="{{ url('convertIncremental/' . $conversion->id . '/' . $index + 1) }}">Skip item and continue to next one</x-link>
                @else
                    <x-primary-button class="ml-0 mt-3">
                        {{ __('Add item to BibTeX file and display BibTeX file') }}
                    </x-primary-button>
                @endif

            </form>
        </div>

        <div class="mt-3">
            <p>
            Search for title in <x-link :href="$googleScholarUrl" target="_blank">Google Scholar</x-link> [new window/tab]
            &nbsp;&bull;&nbsp;
            <x-link :href="$jstorUrl" target="_blank">JSTOR</x-link> [new window/tab]
            </p>
        </div>

        <div class="mt-3">
            <p>
            <x-link href="{{ url('/showBibtex/' . $conversion->id) }}" target="_blank">Display current BibTeX file in new tab/window</x-link>
            &nbsp;&bull;&nbsp;
            <x-link href="{{ url('downloadBibtex/' . $conversion->bib_file_id) }}">download current BibTeX file</x-link>
            </p>
        </div>

        <div class="mt-3">
            <form method="GET" action="{{ route('file.convertIncremental', ['conversionId' => $conversion->id, 'index' => $index]) }}">
                @csrf
        
                <input type="hidden" id="index" name="index" value="{{ $index }}" />
                <input type="hidden" id="source" name="source" value="{{ $bibtexItem['source'] }}" />
                <input type="hidden" id="kind" name="kind" value="{{ $bibtexItem['item']->kind }}" />
                
                <div>
                    <x-input-label value="Source" />
                    <x-textarea-input id="source" name="source" rows="4" :value="$bibtexItem['source']" class="w-full" />
                </div>

                <div>
                    <x-select-input id="itemTypeId" name="itemTypeId" :options="$options" :selected="$selected" />
                </div>

                <div>
                <x-primary-button class="ml-0 mt-3">
                    {{ __('Resubmit') }}
                </x-primary-button>
                </div>
            </form>
        </div>

        <div class="mt-3">
            <p>
            Current version of conversion algorithm: ??? 1.88 (2023-06-03 22:41:12)
            </p>
        </div>
    </div>
</x-app-layout>
