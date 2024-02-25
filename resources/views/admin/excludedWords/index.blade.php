<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Excluded words
        </h2>
    </x-slot>

    <div class="px-4 sm:px-4 sm:rounded-lg">
        <p>
            Strings that are used as abbreviations but are also in the dictionary as words on their own.
        </p>
        <div class="mb-4">
            <x-link href="/admin/excludedWords/create">Add excluded word</x-link>
        </div>
        <ul>
            @foreach ($excludedWords as $excludedWord)
                <li>
                    <form method="POST" action="{{ route('excludedWords.destroy', $excludedWord->id) }}">
                        @method('DELETE')
                        @csrf
                            <x-link href="{{ url('admin/excludedWords/' . $excludedWord->id . '/edit') }}">{{ $excludedWord->word }}</x-link>
                            <x-small-submit-button class="ml-2">
                                {{ 'X' }}
                            </x-small-submit-button>
                    </form>
                </li>
            @endforeach
        </ul>
    </div>

</x-app-layout>
