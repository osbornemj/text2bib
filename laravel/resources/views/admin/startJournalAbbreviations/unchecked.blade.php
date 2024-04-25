<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Unchecked abbreviations used as the first word in journal names
        </h2>
        {{ $uncheckedStartJournalAbbreviations->total()}} found
    </x-slot>

    <div class="px-4 mb-4 sm:px-4 sm:rounded-lg">
        A word is distinctive (purple button) if it cannot plausibly occur at the last word in a title, following a comma.
    </div>

    <div class="px-4 sm:px-4 sm:rounded-lg">
        <div class="mb-4">
            <x-link href="/admin/journals">Checked</x-link>
        </div>

        @if ($uncheckedStartJournalAbbreviations->count())
            <ul>
                @foreach ($uncheckedStartJournalAbbreviations as $startJournalAbbreviation)
                <li>
                    <div>
                    <livewire:start-journal-abbreviation-check :startJournalAbbreviation="$startJournalAbbreviation" :currentPage="$uncheckedStartJournalAbbreviations->currentPage()" />
                    </div>
                </li>
                @endforeach
            </ul>
        @endif

        {{ $uncheckedStartJournalAbbreviations->links() }}
    </div>

</x-app-layout>
