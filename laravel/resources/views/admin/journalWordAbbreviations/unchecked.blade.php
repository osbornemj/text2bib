<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Unchecked abbreviations used in journal names
        </h2>
        {{ $uncheckedJournalWordAbbreviations->total()}} found
    </x-slot>

    <div class="px-4 mb-4 sm:px-4 sm:rounded-lg">
        A word is distinctive (purple button) if it cannot plausibly occur at the last word in a title, following a comma.
    </div>

    <div class="px-4 sm:px-4 sm:rounded-lg">
        <div class="mb-4">
            <x-link href="/admin/journalWordAbbreviations">Checked</x-link>
        </div>

        @if ($uncheckedJournalWordAbbreviations->count())
            <ul>
                @foreach ($uncheckedJournalWordAbbreviations as $journalWordAbbreviation)
                <li>
                    <div>
                    <livewire:journal-word-abbreviation-check :journalWordAbbreviation="$journalWordAbbreviation" :currentPage="$uncheckedJournalWordAbbreviations->currentPage()" type="unchecked"/>
                    </div>
                </li>
                @endforeach
            </ul>
        @endif

        {{ $uncheckedJournalWordAbbreviations->links() }}
    </div>

</x-app-layout>
