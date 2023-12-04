<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('File conversion report') }}
        </h2>
    </x-slot>

    <div class="sm:px-4 lg:px-4 space-y-6">
        <div class="sm:p-0 pt-0 sm:pt-0">
            <p>
                <x-link href="{{ url('/showBibtex/' . $conversion->bib_file_id) }}" target="_blank">Display BibTeX file in new tab/window</x-link>
                &nbsp;&bull;&nbsp;
                <x-link href="{{ url('downloadBibtex/' . $conversion->bib_file_id) }}">download BibTeX file</x-link>
            </p>
            <p>
            Current version of conversion algorithm: ??? 1.88 (2023-06-03 22:41:12)
            </p>
            <p>
                Settings for this conversion:
                <ul class="ml-4">
                    <li>
                        <i>Incremental?</i> {{ $conversion->incremental ? 'Yes' : 'No' }}
                    </li>
                    <li>
                        <i>Item separator</i>: {{ $conversion->item_separator }}
                    </li>
                    <li>
                        <i>First component of each item in your source file</i>: {{ $conversion->first_component }}
                    </li>
                    <li>
                        <i>Label style</i>: @if ($conversion->label_style == 'gs') Google Scholar @else {{ $conversion->label_style }} @endif
                        @if ($conversion->override_labels) (override labels in source file)
                        @else () (do not override labels in source file)
                        @endif
                    </li>
                    <li>
                        <i>Line-ending style for output</i>: {{ $conversion->line_endings == 'w' ? 'Windows' : 'Linux' }}
                    </li>
                    <li>
                        <i>Treat % as starting a comment?</i> {{ $conversion->percent_comment ? 'Yes' : 'No' }}
                    </li>
                    <li>
                        <i>Include each reference as comment above entry in BibTeX file?</i> {{ $conversion->include_source ? 'Yes' : 'No' }}
                    </li>
                </ul>
            </p>
            <p>
                {{ count($bibtexItems) }} references found in your file.
                </p>
            </div>
        <div class="mt-3">
            @foreach ($bibtexItems as $i => $bibtexItem)
            <ul class="py-3 border-t-4">
                <li>
                    <span class="font-bold text-blue-500">Reference {{ $i+1 }}</span>: {{ $bibtexItem['source'] }}
                </li>
                <li>
                    <span class="ml-4 text-blue-500">Type</span>: {{ $bibtexItem['item']->kind }}
                </li>
                <li>
                    <span class="ml-4 text-blue-500">Label</span>: {{ $bibtexItem['item']->label }}
                </li>
                <li>
                    <span class="ml-4 text-blue-500">BibTeX entry</span>:
                </li>
                @foreach ($bibtexItem['item'] as $key => $content)
                    @if (!in_array($key, ['kind', 'label']))
                        <li><span class="px-1 ml-8 bg-blue-200 dark:text-gray-800">{{ $key }}</span> {{ $content }}</li>
                    @endif
                @endforeach
                @foreach ($bibtexItem['warnings'] as $content)
                    <li><span class="ml-4 text-red-500">Warning</span>: {{ $content }}</li>
                @endforeach
                @foreach ($bibtexItem['notices'] as $content)
                    <li><span class="ml-4 text-orange-300">Notice</span>: {{ $content }}</li>
                @endforeach

                @if ($verbose)
                <li class="mt-4">
                    <i>Details of conversion</i>
                </li>
                    @foreach ($bibtexItem['details'] as $details)
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
                                    @case ('item')
                                        <span class="text-violet-500">{{ $value }}</span>
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
                @endif
            </ul>
            @endforeach
        </div>
    </div>
</x-app-layout>
