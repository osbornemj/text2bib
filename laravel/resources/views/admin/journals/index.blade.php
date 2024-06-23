<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Checked journals
        </h2>
        {{ $checkedJournals->total()}} found
    </x-slot>

    <div class="px-4 mb-4 sm:px-4 sm:rounded-lg">
        A journal name is distinctive (purple button) if it cannot plausibly occur in the title of an item.
    </div>

    <div class="px-4 sm:px-4 sm:rounded-lg">
        <div class="mb-4">
            <x-link href="/admin/journals/create">Add journal</x-link>
            &nbsp;&bull;&nbsp;
            <x-link href="/admin/uncheckedJournals">Unchecked</x-link>
        </div>

        @if ($checkedJournals->count())
            <ul>
                @foreach ($checkedJournals as $journal)
                <li>
                    <div>
                        <livewire:journal-check :journal="$journal" type="checked" :currentPage="$checkedJournals->currentPage()" />
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif

        {{ $checkedJournals->links() }}
    </div>

</x-app-layout>
