<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Error reports') }}
        </h2>
    </x-slot>

    @foreach ($errorReports as $errorReport)
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-8 sm:ml-4">
            <div class="col-span-5">
                <x-link href="{{ url('errorReport/' . $errorReport->id) }}">{{ $errorReport->created_at }}: {{ $errorReport->title }}</x-link> 
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
