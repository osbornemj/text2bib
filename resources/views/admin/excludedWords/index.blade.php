<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Excluded words
        </h2>
    </x-slot>

    <div class="sm:px-0 lg:px-0 space-y-6">
        <div class="px-4 sm:px-4 pt-0 sm:pt-0 sm:rounded-lg">
            <a href="/admin/excludedWords/create">Add excluded word</a>
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
    </div>

</x-app-layout>
