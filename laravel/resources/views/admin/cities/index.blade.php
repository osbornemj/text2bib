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
        &nbsp;&bull;&nbsp;
        <x-link href="/admin/uncheckedCities">Unchecked</x-link>

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

        {{ $checkedCities->links() }}
    </div>

</x-app-layout>
