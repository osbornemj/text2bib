<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Publishers
        </h2>
        {{ $checkedPublishers->total()}} found
    </x-slot>

    <div class="px-4 sm:px-4 sm:rounded-lg">
        A name is distinctive (purple button) if it cannot plausibly occur in the title of an item.
    </div>

    <div class="px-4 sm:px-4 sm:rounded-lg">
        <div class="mb-4">
            <x-link href="/admin/publishers/create">Add publisher</x-link>
            &nbsp;&bull;&nbsp;
            <x-link href="/admin/uncheckedPublishers">Unchecked</x-link>
        </div>

        @if ($checkedPublishers->count())
            <h3 class="mt-4 font-semibold text-lg leading-tight">Checked</h3>
            <ul>
                @foreach ($checkedPublishers as $publisher)
                <li>
                    <div>
                    <livewire:publisher-check :publisher="$publisher" type="checked" :currentPage="$checkedPublishers->currentPage()" />
                    </div>
                </li>
                @endforeach
            </ul>
        @endif

        {{ $checkedPublishers->links() }}
    </div>

</x-app-layout>
