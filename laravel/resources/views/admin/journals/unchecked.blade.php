<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Unchecked journals
        </h2>
        {{ $uncheckedJournals->total()}} found
    </x-slot>

    <div class="px-4 mb-4 sm:px-4 sm:rounded-lg">
        A journal name is distinctive (purple button) if it cannot plausibly occur in the title of an item.
    </div>

    <div class="px-4 sm:px-4 sm:rounded-lg">
        <div class="mb-4">
            <x-link href="/admin/journals">Checked</x-link>
        </div>

        @if ($uncheckedJournals->count())
            <ul>
                @foreach ($uncheckedJournals as $journal)
                <li>
                    <div>
                    <livewire:journal-check :journal="$journal" type="unchecked" :currentPage="$uncheckedJournals->currentPage()" />
                    </div>
                </li>
                @endforeach
            </ul>
        @endif

        {{ $uncheckedJournals->links() }}
    </div>

</x-app-layout>
