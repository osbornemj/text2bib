<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Error reports') }}
        </h2>
    </x-slot>

    @if (!count($errorReports))
        <div class="mx-4">
            None so far.
        </div>
    @else
        <div class="mx-4 mb-2">
            "Waiting" = waiting for the poster of the report to respond to a clarifactory question.  Posting a response on a closed thread reopens the thread.
        </div>
    @endif

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-12 mx-4 border-b mb-1">
        <div class="col-span-6 mb-1">
            Title
        </div>
        <div class="col-span-2 mb-1">
            <x-link href="{{ url('errorReports/latest') }}">
                @if ($sortBy == 'latest')
                    <b>
                @endif
                    Latest
                @if ($sortBy == 'latest')
                    </b>
                @endif
            </x-link>
        </div>
        <div class="col-span-3 mb-1">
            <x-link href="{{ url('errorReports/poster') }}">
                @if ($sortBy == 'poster')
                    <b>
                @endif
                    Poster
                @if ($sortBy == 'poster')
                    </b>
                @endif
            </x-link>
        </div>
        <div class="col-span-1 mb-1">
            <x-link href="{{ url('errorReports/status') }}">
                @if ($sortBy == 'status')
                    <b>
                @endif
                    Status
                @if ($sortBy == 'status')
                    </b>
                @endif
            </x-link>
        </div>
    </div>
    @foreach ($errorReports as $errorReport)
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-12 mx-4">
            <div class="col-span-6">
                <x-link href="{{ url('errorReport/' . $errorReport->id) }}">{{ substr($errorReport->output->source, 0, strpos($errorReport->output->source, ' ', 45)) . ' ...' }}</x-link>
            </div>
            <div class="col-span-2">
                {{ $errorReport->updated_at->format('Y-m-d') }}
            </div>
            <div class="mt-0 pt-0 col-span-3">
                {{ $errorReport->output->conversion->user->fullName() }}
            </div>
            <div class="mt-0 pt-0 col-span-1">
                <span class="{{ $errorReport->status->color() }}">{{ $errorReport->status->name }}
            </div>
        </div>
    @endforeach

</x-app-layout>
