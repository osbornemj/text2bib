<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Unchecked publishers
        </h2>
        {{ $uncheckedPublishers->total()}} found
    </x-slot>

    <div class="px-4 mb-0 sm:px-4 sm:rounded-lg">
        A city is distinctive (purple button) if it cannot plausibly occur in the title of an item.
    </div>

    <div class="px-4 sm:px-4 sm:rounded-lg">
        <div class="mb-4">
            <x-link href="/admin/publishers">Checked</x-link>
        </div>

        @if ($uncheckedPublishers->count())
            <ul>
                @foreach ($uncheckedPublishers as $publisher)
                <li>
                    <div>
                    <livewire:publisher-check :publisher="$publisher" type="unchecked" :currentPage="$uncheckedPublishers->currentPage()" />
                    </div>
                </li>
                @endforeach
            </ul>
        @endif

        {{ $uncheckedPublishers->links() }}
    </div>

</x-app-layout>
