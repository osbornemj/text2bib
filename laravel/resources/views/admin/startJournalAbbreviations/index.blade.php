<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Abbreviations used as first word of journal name
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
        <x-link href="/admin/startJournalAbbreviations/create">Add abbreviation used as first word in journal name</x-link>
        @if ($uncheckedStartJournalAbbreviations->count())
            <h3 class="mt-4 font-semibold text-lg leading-tight">Unchecked</h3>
            <ul>
                @foreach ($uncheckedStartJournalAbbreviations as $startJournalAbbreviation)
                <li>
                    <div>
                    <livewire:start-journal-abbreviation-check :startJournalAbbreviation="$startJournalAbbreviation" />
                    </div>
                </li>
                @endforeach
            </ul>
        @endif

        @if ($checkedStartJournalAbbreviations->count())
            <h3 class="mt-4 font-semibold text-lg leading-tight">Checked</h3>
            <ul>
                @foreach ($checkedStartJournalAbbreviations as $startJournalAbbreviation)
                <li>
                    <div>
                        <livewire:start-journal-abbreviation-check :startJournalAbbreviation="$startJournalAbbreviation" />
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif

    </div>

</x-app-layout>
