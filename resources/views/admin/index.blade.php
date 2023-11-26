<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Admin') }}
        </h2>
    </x-slot>

    <div class="sm:px-4 lg:px-4 space-y-6">
        <div class="sm:p-0 pt-0 sm:pt-0">
            <x-link :href="route('itemTypes.index')" :active="request()->routeIs('itemTypes.index')">
                Types and fields
            </x-link>
            <p>
                <x-link :href="route('examples.index')" :active="request()->routeIs('examples.index')">
                    Examples
                </x-link>
            </p>
            <p>
                <x-link :href="route('admin.users')" :active="request()->routeIs('admin.users')">
                    Users
                </x-link>
            </p>
            <p>
                <x-link :href="route('admin.conversions')" :active="request()->routeIs('admin.conversions')">
                    Conversions
                </x-link>
            </p>
            <p>
                <x-link :href="route('vonNames.index')" :active="request()->routeIs('vonNames.index')">
                    von Names
                </x-link>
            </p>
            <p>
                <x-link :href="route('publishers.index')" :active="request()->routeIs('publishers.index')">
                    Publishers
                </x-link>
            </p>
            <p>
                <x-link :href="route('cities.index')" :active="request()->routeIs('cities.index')">
                    Publication cities
                </x-link>
            </p>
            <p>
                <x-link :href="route('names.index')" :active="request()->routeIs('names.index')">
                    Names
                </x-link>
                (proper names that need to have their initial letter enclosed in braces in titles)
            </p>
            <p>
                <x-link :href="route('excludedWords.index')" :active="request()->routeIs('excludedWords.index')">
                    Excluded words
                </x-link>
                (words that are in the dictionary but are not standard words and are used as abbreviations in journal names)
            </p>
        </div>
    </div>
</x-app-layout>
