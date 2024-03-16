<x-app-layout>

    <div class="sm:px-0 pt-4 space-y-6">
        <div class="px-4 pt-0 sm:rounded-lg">
                <x-link href="{{ url('admin/conversions') }}">All conversions</x-link>
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

            @foreach ($outputs as $i => $output)
                <div class="mt-4">
                    <x-link href="{{ url('admin/formatExample/' . $output->id)}}" target="_blank">Format for Examples Seeder</x-link>
                </div>
                <div class="mt-2">
                    {{ $output->source }}
                </div>
                <div class="mt-2">
                    @if ($output->correctness == -1) 
                        @if ($output->rawOutput)
                            <span class="bg-blue-600">Corrected</span>
                        @else                    
                            <span class="bg-red-500">Incorrect</span>
                        @endif
                    @elseif ($output->correctness == 1)
                        <span class="bg-emerald-500">Correct</span>
                    @else 
                        <span class="bg-slate-400">Unrated</span>
                    @endif

                    {{ '@' }}{{ $output->itemType->name }}{{ '{' }}{{ $output->label }},
                    @foreach ($convertedItems[$i] as $name => $content)
                        <div class="ml-6">
                            {{ $name }} = {{ '{' }}{{ $content }}{{ '}' }},
                        </div>
                    @endforeach
                    {{ '}' }}
                </div>

                @if ($output->rawOutput)
                    <div class="mt-2">
                        @if ($output->correctness == -1) 
                            <span class="bg-red-500">Original</span>
                        @endif

                        {{ '@' }}{{ $output->rawOutput->itemType->name }}{{ '{' }}{{ $output->rawOutput->label }},
                        @foreach ($output->rawOutput->item as $name => $content)
                            <div class="ml-6">
                                {{ $name }} = {{ '{' }}{{ $content }}{{ '}' }},
                            </div>
                        @endforeach
                    {{ '}' }}
                    </div>
                @endif

            @endforeach
        </div>
    </div>

</x-app-layout>
