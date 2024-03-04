<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Add comment
        </h2>
    </x-slot>

    <div class="sm:px-0 lg:px-0 space-y-6">
        <div class="px-4 sm:px-4 pt-0 sm:pt-0 sm:rounded-lg">
            <form method="POST" action="{{ route('threads.store') }}">
                @csrf

                <div>
                    <x-input-label for="name" :value="__('Title')" />
                    <x-text-input id="title" class="block mt-1 w-full" name="title" :value="old('title')" autofocus />
                    <div role="alert" class="mt-4 mb-4">
                        @error('title') <span class="text-red-600 dark:text-red-500">{{ $message }}</span> @enderror 
                    </div>
                </div>

                <x-input-label for="content" value="Comment" />
                <x-textarea-input rows="5" id="content" class="block mt-1 w-full" name="content" :value="old('content')"/>
                <div role="alert" class="mt-4 mb-4">
                    @error('content') <span class="text-red-600 dark:text-red-500">{{ $message }}</span> @enderror 
                </div>

                <div class="flex items-center mt-4">
                    <x-primary-button class="ml-0">
                        {{ __('Submit') }}
                    </x-primary-button>
                </div>

                <div class="flex items-center mt-4">
                    <x-link href="{{ url('threads') }}">All comments</x-link>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>
