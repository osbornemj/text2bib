<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Names in dictionary
        </h2>
    </x-slot>

    <div class="px-4 sm:px-4 sm:rounded-lg">
        <p>
            Strings that are names but are in the dictionary starting with lowercase letters.
        </p>
        <div class="mb-4">
            <x-link href="/admin/dictionaryNames/create">Add name that is in dictionary</x-link>
        </div>
        <ul>
            @foreach ($dictionaryNames as $dictionaryName)
                <li>
                    <form method="POST" action="{{ route('dictionaryNames.destroy', $dictionaryName->id) }}">
                        @method('DELETE')
                        @csrf
                            <x-link href="{{ url('admin/dictionaryNames/' . $dictionaryName->id . '/edit') }}">{{ $dictionaryName->word }}</x-link>
                            <x-small-submit-button class="ml-2 bg-red-400 dark:bg-red-800">
                                {{ 'X' }}
                            </x-small-submit-button>
                    </form>
                </li>
            @endforeach
        </ul>
    </div>

</x-app-layout>
