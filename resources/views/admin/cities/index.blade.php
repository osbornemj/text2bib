<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Cities
        </h2>
    </x-slot>

    <div class="px-4 sm:px-4 sm:rounded-lg">
        A name is distinctive (purple button) if it cannot plausibly occur in the title of an item.
    </div>

    <div class="px-4 sm:px-4 sm:rounded-lg">
        <x-link href="/admin/cities/create">Add city</x-link>
        @if ($uncheckedCities->count())
            <h3 class="mt-4 font-semibold text-lg leading-tight">Unchecked</h3>
            <ul>
                @foreach ($uncheckedCities as $city)
                <li>
                    <div>
                    <livewire:city-check :city="$city" />
                    </div>
                </li>
                @endforeach
            </ul>
        @endif

        @if ($checkedCities->count())
            <h3 class="mt-4 font-semibold text-lg leading-tight">Checked</h3>
            <ul>
                @foreach ($checkedCities as $city)
                <li>
                    <div>
                        <livewire:city-check :city="$city" />
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif

    </div>

</x-app-layout>
