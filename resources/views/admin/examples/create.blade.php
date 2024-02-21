<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Add example
        </h2>
    </x-slot>

    <div class="sm:px-0 lg:px-0 space-y-6">
        <div class="px-4 sm:px-4 sm:rounded-lg">
            <form method="POST" action="{{ route('examples.store') }}">
                @csrf

                <div>
                    <x-input-label for="source" :value="__('Source')" />
                    <x-textarea-input rows="6" cols="80" id="source" class="block mt-1" name="source" :value="old('source')" required autofocus />
                    <x-input-error :messages="$errors->get('source')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="bibtex" :value="__('BibTeX')" />
                    <x-textarea-input rows="12" cols="80" id="bibtex" class="block mt-1" name="bibtex" :value="old('bibtex')" required />
                    <x-input-error :messages="$errors->get('bibtex')" class="mt-2" />
                </div>

                <div class="flex items-center mt-4">
                    <x-primary-button class="ml-0">
                        {{ __('Save') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
