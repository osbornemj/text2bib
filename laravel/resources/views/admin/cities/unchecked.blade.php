<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Unchecked cities
        </h2>
        {{ $uncheckedCities->total()}} found
    </x-slot>

    <div class="px-4 mb-0 sm:px-4 sm:rounded-lg">
        A city is distinctive (purple button) if it cannot plausibly occur in the title of an item.
    </div>

    <div class="px-4 sm:px-4 sm:rounded-lg">
        <div class="mb-4">
            <x-link href="/admin/cities">Checked</x-link>
        </div>

        @if ($uncheckedCities->count())
            <ul>
                @foreach ($uncheckedCities as $city)
                <li>
                    <div>
                    <livewire:city-check :city="$city" type="unchecked" :currentPage="$uncheckedCities->currentPage()" />
                    </div>
                </li>
                @endforeach
            </ul>
        @endif

        {{ $uncheckedCities->links() }}
    </div>

</x-app-layout>
