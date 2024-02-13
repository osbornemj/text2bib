<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Error reports') }}
        </h2>
    </x-slot>

    @if (!count($errorReports))
        <div class="ml-4">
            None so far.
        </div>
    @endif

    @foreach ($errorReports as $errorReport)
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-12 sm:ml-4">
            <div class="col-span-7">
                <x-link href="{{ url('errorReport/' . $errorReport->id) }}">{{ substr($errorReport->output->source, 0, strpos($errorReport->output->source, ' ', 60)) . ' ...' }}</x-link>
            </div>
            <div class="col-span-2">
                {{ $errorReport->created_at->format('Y-m-d h:m') }}
            </div>
            <div class="mt-0 pt-0 col-span-2">
                {{ $errorReport->output->conversion->user->fullName() }}
            </div>
            <div class="mt-0 pt-0 col-span-1">
                {{ $errorReport->status->name }}
            </div>
        </div>
    @endforeach

</x-app-layout>
