<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Add journal word abbreviation
        </h2>
    </x-slot>

    <div class="sm:px-0 lg:px-0 space-y-6">
        <div class="px-4 sm:px-4 pt-0 sm:pt-0 sm:rounded-lg">
            <form method="POST" action="{{ route('journalWordAbbreviations.store') }}">
                @csrf

                <div>
                    <x-input-label for="word" :value="__('Word')" />
                    <x-text-input id="word" class="block mt-1" name="word" :value="old('word')" required autofocus />
                    <x-input-error :messages="$errors->get('word')" class="mt-2" />
                </div>

                <div class="flex items-center mt-4">
                    <x-primary-button class="ml-0">
                        {{ __('Save') }}
                    </x-primary-button>
                </div>

                <div class="flex items-center mt-4">
                    <x-link href="{{ url('/admin/journalWordAbbreviations') }}">All journal word abbreviations</x-link>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>
