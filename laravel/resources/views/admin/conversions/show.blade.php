<x-app-layout>

    <div class="sm:px-0 pt-4 space-y-6">
        <div class="px-4 pt-0 sm:rounded-lg">
                All conversions: <x-link href="{{ url('admin/conversions?page=' . $page) }}">current page</x-link>
                &nbsp;&bull;&nbsp;
                <x-link href="{{ url('admin/conversions') }}">first page</x-link>
        </div>
    </div>

    <div class="sm:px-0 pt-4 space-y-6">
        <div class="px-4 sm:rounded-lg">
            <div class="ml-0">
                @if ($conversion->user)
                    {{ $conversion->user->fullName() }}
                    &nbsp;&bull;&nbsp;
                @endif
                {{ $conversion->created_at }}
                &nbsp;&bull;&nbsp;
                <x-link href="{{ url('admin/downloadSource/' . $conversion->user_file_id) }}">source file</x-link>
                @if ($conversion->version)
                    &nbsp;&bull;&nbsp;
                    code version {{ $conversion->version }}
                @endif
                <br/>
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
                <br/>
                @if ($conversion->examined_at)
                <div class="mt-1">
                    <span class="text-black bg-emerald-500 px-2 text-sm uppercase rounded">Examined {{ $conversion->examined_at }}</span>
                    [<x-link href="{{ url('admin/conversionUnexamined/' . $conversion->id) }}">remove</x-link>]
                </div>
                @else
                    <x-link href="{{ url('admin/conversionExamined/' . $conversion->id . '/' . $page) }}">Record conversion examined</x-link>
                @endif
            </div>

            @foreach ($outputs as $i => $output)
                <div>
                    <livewire:admin-converted-item :output="$output" />
                </div>
            @endforeach
        </div>
    </div>

</x-app-layout>
