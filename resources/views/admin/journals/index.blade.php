<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Journals
        </h2>
    </x-slot>

    <div class="px-4 sm:px-4 sm:rounded-lg">
        <x-link href="/admin/journals/create">Add journal</x-link>
        @if ($uncheckedJournals->count())
            <h3 class="mt-4 font-semibold text-lg leading-tight">Unchecked</h3>
            <ul>
                @foreach ($uncheckedJournals as $journal)
                <li>
                    <div>
                    <livewire:journal-check :journal="$journal" />
                    </div>
                </li>
                @endforeach
            </ul>
        @endif

        @if ($checkedJournals->count())
            <h3 class="mt-4 font-semibold text-lg leading-tight">Checked</h3>
            <ul>
                @foreach ($checkedJournals as $journal)
                <li>
                    <div>
                        <livewire:journal-check :journal="$journal" />
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif

    </div>

</x-app-layout>
