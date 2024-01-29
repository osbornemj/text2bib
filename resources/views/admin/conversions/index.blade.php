<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Conversions
        </h2>
    </x-slot>

    <div class="sm:px-0 lg:px-0 space-y-6">
        <div class="px-4 sm:px-4 pt-0 sm:pt-0 sm:rounded-lg">
            <ul>
            @foreach ($conversions as $conversion)
                <li>
                    <x-link href="{{ url('/admin/showFile/' . $conversion->bib_file_id) }}">BibTeX file</x-link>
                    &nbsp;&bull;&nbsp;
                    <x-link href="{{ url('/admin/showFile/' . $conversion->user_file_id) }}">source file</x-link>
                    &nbsp;&bull;&nbsp;
                    <x-link href="{{ url('/admin/convert/' . $conversion->user_file_id) }}">convert (line sep)</x-link>
                    &nbsp;&bull;&nbsp;
                    <x-link href="{{ url('/admin/convert/' . $conversion->user_file_id . '/cr') }}">convert (cr sep)</x-link>
                    <br/>
                    <div class="ml-4">
                        {{ $conversion->userFile->user->fullName() }}
                        &nbsp;&bull;&nbsp;
                        {{ $conversion->created_at }}
                        &nbsp;
                        <div class="flex items-center">
                            @for ($i = 0; $i < $conversion->rating; $i++)
                            <svg class="w-4 h-4 fill-current text-yellow-600"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z">
                                </path>
                            </svg>
                            @endfor
                        </div>
                        sep = {{ $conversion->item_separator}}
                        &nbsp;&bull;&nbsp;
                        first = {{ $conversion->first_component }}
                        &nbsp;&bull;&nbsp;
                        labels = {{ $conversion->label_style }}
                        &nbsp;&bull;&nbsp;
                        line endings = {{ $conversion->line_endings }}
                        <br/>
                        {{ $conversion->char_encoding }}
                        &nbsp;&bull;&nbsp;
                        {{ $conversion->percent_comment ? '% = comment' : '% != comment' }}
                        &nbsp;&bull;&nbsp;
                        {{ $conversion->include_source ? 'include source' : 'no source' }}
                        &nbsp;&bull;&nbsp;
                        {{ $conversion->report_type }} report
                    </div>
                </li>
            @endforeach
            </ul>
        </div>
        {{ $conversions->links() }}
    </div>
</x-app-layout>
