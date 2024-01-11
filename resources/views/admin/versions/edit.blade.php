<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Edit version
        </h2>
    </x-slot>

    <div class="sm:px-0 lg:px-0 space-y-6">
        <div class="px-4 sm:px-4 pt-0 sm:pt-0 sm:rounded-lg">
            <form method="POST" action="{{ route('versions.update', $version->id) }}">
                @method('PUT')
                @csrf

                <div>
                    <x-input-label for="version" :value="__('Name')" />
                    <x-text-input id="version" class="block mt-1" name="version" :value="old('version', $version->name)" required autofocus />
                    <x-input-error :messages="$errors->get('version')" class="mt-2" />
                </div>

                <div class="flex items-center mt-4">
                    <x-primary-button class="ml-0">
                        {{ __('Save') }}
                    </x-primary-button>
                </div>
            </form>

            <div class="flex items-center mt-4">
                <x-link href="{{ url('/admin/versions') }}">All versions</x-link>
            </div>

        </div>
    </div>
</x-app-layout>
