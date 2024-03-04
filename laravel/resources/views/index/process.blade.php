<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Process items') }}
        </h2>
    </x-slot>

    <div class="sm:px-4 lg:px-4 space-y-6">
        <div class="sm:p-0 pt-0 sm:pt-0">
            <div>
                <livewire:process-items />
            </div>
        </div>
    </div>

</x-app-layout>

