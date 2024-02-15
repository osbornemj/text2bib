<x-app-layout>

    <div class="sm:px-0 lg:px-0 pt-4 space-y-6">
        <div class="px-4 sm:px-4 pt-0 sm:pt-0 sm:rounded-lg">
            <div class="ml-0">
                {{ $conversion->user->fullName() }}
                &nbsp;&bull;&nbsp;
                {{ $conversion->created_at }}
                &nbsp;&bull;&nbsp;
                sep = {{ $conversion->item_separator}}
                &nbsp;&bull;&nbsp;
                first = {{ $conversion->first_component }}
                &nbsp;&bull;&nbsp;
                labels = {{ $conversion->label_style }}
                <br/>
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

            @foreach ($outputs as $output)
                <div class="mt-4">
                    {{ $output->source }}
                </div>
                <div class="mt-4">
                    @if ($output->correctness == -1) 
                        <span class="bg-red-500">Incorrect</span>
                    @elseif ($output->correctness == 1)
                        <span class="bg-emerald-500">Correct</span>
                    @else 
                        <span class="bg-slate-400">Unrated</span>
                    @endif

                    {{ '@' }}{{ $output->itemType->name }}{{ '{' }}{{ $output->label }},
                    @foreach ($output->item as $name => $content)
                        <div class="ml-6">
                            {{ $name }} = {{ '{' }}{{ $content }}{{ '}' }},
                        </div>
                    @endforeach
                    }
                </div>
            @endforeach
        </div>
    </div>

</x-app-layout>
