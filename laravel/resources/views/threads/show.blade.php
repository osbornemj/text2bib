<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Comment
        </h2>
    </x-slot>

    <h2 class="ml-4 text-lg text-blue-600 dark:text-blue-500">
        {{ $thread->title }}
    </h2>

    <div>
        <livewire:comments :thread="$thread" :opUser="$opUser" :type="$type" />
    </div>

    <div class="sm:px-0 lg:px-0 space-y-6">
        <div class="px-4 pt-0 sm:pt-0 sm:rounded-lg">

            <div class="flex items-center mt-4">
                <x-link href="{{ url('comments') }}">All comments</x-link>
            </div>

        </div>
    </div>
</x-app-layout>
