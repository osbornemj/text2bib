<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Admin') }}
        </h2>
    </x-slot>

    <div class="px-4 space-y-6">
        <div class="pt-0">
            <h3 class="font-semibold text-l">
                Versions
            </h3>
            <p>
                Current: {{ $latestVersion }}.  <x-link href="{{ url('admin/addVersion') }}">Add version</x-link>.
            </p>

            <h3 class="font-semibold text-l mt-2">
                Examples
            </h3>
            <p>
                The database table <code>examples</code> contains the source and correct BibTeX code for a large number of references.
            </p>
            <p>
                <x-link :href="route('examples.index')" :active="request()->routeIs('examples.index')">
                    View, check, and edit examples
                </x-link>
            </p>
            <p>
                @php
                    $sameDay = $trainingItemsConversionStartedAt->isSameDay($trainingItemsConversionEndedAt);
                @endphp
                <x-link :href="route('admin.trainingItems.index')">
                    Training data
                </x-link>
                ({{ number_format($trainingItemsConversionCount) }} items converted in last conversion, {{ $trainingItemsConversionStartedAt->format('Y-m-d H:i') }}
                @if ($trainingItemsConversionEndedAt)
                    to
                    @if ($sameDay)
                        {{ $trainingItemsConversionEndedAt->format('H:i') }}
                    @else
                        {{ $trainingItemsConversionEndedAt->format('Y-m-d H:i') }}
                    @endif
                    => {{ $itemsPerSecond }} items per second)
                @else
                    currently underway)
                @endif
            </p>

            <h3 class="font-semibold text-l mt-2">
                Users and conversions
            </h3>
            <p>
                <x-link :href="route('admin.users')" :active="request()->routeIs('admin.users')">
                    Users
                </x-link>
            </p>
            <p>
                Conversions:&nbsp;
                <x-link :href="route('admin.conversions')">
                    all
                </x-link>
                &nbsp;&bull;&nbsp;
                <x-link href="{{ url('admin/conversions/0/unchecked') }}">
                    unchecked
                </x-link>
                ({{ $uncheckedConversionCount }})
                &nbsp;&bull;&nbsp;
                <x-link href="{{ url('admin/conversions/0/compact') }}">
                    compact display                    
                </x-link>
                &nbsp;&bull;&nbsp;
                <x-link href="{{ url('admin/conversions/0/lowercase') }}">
                    items starting with lowercase letter
                </x-link>
            </p>

            <h3 class="font-semibold text-l mt-2">
                Information in database tables used by conversion algorithm
            </h3>
            <p>
                <x-link :href="route('bsts.index')" :active="request()->routeIs('bsts.index')">
                    BibTeX style files
                </x-link>
                @if ($uncheckedBstCount)
                    <span class="bg-red-300 dark:bg-red-500 text-xs px-1">{{ $uncheckedBstCount }}</span>
                    <x-link :href="route('admin.bsts.unchecked')" :active="request()->routeIs('admin.bsts.unchecked')">
                        unchecked
                    </x-link>
                @endif
                (information about the features of the styles relevant to the algorithm)
            </p>
            <p>
                <x-link :href="route('itemTypes.index')" :active="request()->routeIs('itemTypes.index')">
                    Types and fields
                </x-link>
                (the BibTeX item types detected by the algorithm and the fields associated with them)
            </p>
            <p>
                <x-link :href="route('journals.index')" :active="request()->routeIs('journals.index')">
                    Journals
                </x-link>
                @if ($uncheckedJournalCount)
                    <span class="bg-red-300 dark:bg-red-500 text-xs px-1">{{ $uncheckedJournalCount }}</span>
                    <x-link :href="route('admin.journals.unchecked')" :active="request()->routeIs('admin.journals.unchecked')">
                        unchecked
                    </x-link>
                @endif
                (journal names, based on conversions marked as correct by users and approved by administrator)
            </p>
            <p>
                <x-link :href="route('journalWordAbbreviations.index')" :active="request()->routeIs('journalWordAbbreviations.index')">
                    Abbreviations used in journal names
                </x-link>
                @if ($uncheckedJournalWordAbbreviationCount)
                    <span class="bg-red-300 dark:bg-red-500 text-xs px-1">{{ $uncheckedJournalWordAbbreviationCount }}</span>
                    <x-link :href="route('admin.journalWordAbbreviations.unchecked')" :active="request()->routeIs('admin.journalWordAbbreviations.unchecked')">
                        unchecked
                    </x-link>
                @endif
                {{--
                &nbsp;
                &bull;
                &nbsp;
                <x-link :href="route('admin.journalWordAbbreviations.populate')" :active="request()->routeIs('admin.journalWordAbbreviations.populate')">
                    Add (as unchecked) all abbreviations in converted items
                </x-link>
                --}}
            </p>
            <p>
                <x-link :href="route('vonNames.index')" :active="request()->routeIs('vonNames.index')">
                    von Names
                </x-link>
                (name parts like 'von' and 'della' that need special treatment)
            </p>
            <p>
                <x-link :href="route('publishers.index')" :active="request()->routeIs('publishers.index')">
                    Publishers
                </x-link>
                @if ($uncheckedPublisherCount)
                    <span class="bg-red-300 dark:bg-red-500 text-xs px-1">{{ $uncheckedPublisherCount }}</span>
                @endif
                (publishing companies, based on conversions marked as correct by users and approved by administrator)
            </p>
            <p>
                <x-link :href="route('cities.index')" :active="request()->routeIs('cities.index')">
                    Publication cities
                </x-link>
                @if ($uncheckedCityCount)
                    <span class="bg-red-300 dark:bg-red-500 text-xs px-1">{{ $uncheckedCityCount }}</span>
                @endif
                (cities in which publishers are located, for the <code>address</code> field of a book)
            </p>
            <p>
                <x-link :href="route('names.index')" :active="request()->routeIs('names.index')">
                    Names
                </x-link>
                (proper names that need to have their initial letter enclosed in braces in BibTeX entries)
            </p>
            <p>
                <x-link :href="route('excludedWords.index')" :active="request()->routeIs('excludedWords.index')">
                    Excluded words
                </x-link>
                (strings that are used as abbreviations but are also in the dictionary as words on their own)
            </p>
            <p>
                <x-link :href="route('dictionaryNames.index')" :active="request()->routeIs('dictionaryNames.index')">
                    Dictionary names
                </x-link>
                (strings that are names but are in the dictionary as words starting with a lowercase letter)
            </p>

            <h3 class="font-semibold text-l mt-2">
                phpinfo
            </h3>
            <p>
                <x-link :href="route('admin.phpinfo')" :active="request()->routeIs('admin.phpinfo')">
                    phpinfo
                </x-link>
            </p>
        </div>
    </div>
</x-app-layout>
