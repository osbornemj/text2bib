<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Publishers
        </h2>
    </x-slot>

    <div class="px-4 sm:px-4 sm:rounded-lg">
        A name is distinctive (purple button) if it cannot plausibly occur in the title of an item.
    </div>

    <div class="px-4 sm:px-4 sm:rounded-lg">
        <x-link href="/admin/publishers/create">Add publisher</x-link>
        @if ($uncheckedPublishers->count())
            <h3 class="mt-4 font-semibold text-lg leading-tight">Unchecked</h3>
            <ul>
                @foreach ($uncheckedPublishers as $publisher)
                <li>
                    <div>
                    <livewire:publisher-check :publisher="$publisher" />
                    </div>
                </li>
                @endforeach
            </ul>
        @endif

        @if ($checkedPublishers->count())
            <h3 class="mt-4 font-semibold text-lg leading-tight">Checked</h3>
            <ul>
                @foreach ($checkedPublishers as $publisher)
                <li>
                    <div>
                        <livewire:publisher-check :publisher="$publisher" />
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif

    </div>

</x-app-layout>
