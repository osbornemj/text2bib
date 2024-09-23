<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Abbreviations used in journal names
        </h2>
    </x-slot>

    <div class="px-4 sm:px-4 sm:rounded-lg">
        A word is distinctive (purple button) if it cannot plausibly occur as the last word in a title, following a comma.
    </div>

    {{--
    <div class="px-4 my-2 sm:px-4 sm:rounded-lg">
        <x-link href="/admin/addExistingStarts">Add words from existing journals</x-link>
    </div>
    --}}

    <div class="px-4 sm:px-4 sm:rounded-lg">
        <x-link href="/admin/journalWordAbbreviations/create">Add abbreviation used in journal name</x-link>
        @if ($uncheckedJournalWordAbbreviations->count())
            <h3 class="mt-4 font-semibold text-lg leading-tight">Unchecked</h3>
            <ul>
                @foreach ($uncheckedJournalWordAbbreviations as $journalWordAbbreviation)
                <li>
                    <div>
                    <livewire:journal-word-abbreviation-check :journalWordAbbreviation="$journalWordAbbreviation" type="unchecked" />
                    </div>
                </li>
                @endforeach
            </ul>
        @endif

        @if ($checkedJournalWordAbbreviations->count())
            <h3 class="mt-4 font-semibold text-lg leading-tight">Checked</h3>
            <ul>
                @foreach ($checkedJournalWordAbbreviations as $journalWordAbbreviation)
                    <li>
                        <div>
                            <livewire:journal-word-abbreviation-check :journalWordAbbreviation="$journalWordAbbreviation" type="checked" />
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif

    </div>

</x-app-layout>
