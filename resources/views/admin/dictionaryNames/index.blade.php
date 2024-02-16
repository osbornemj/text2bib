<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Names in dictionary
        </h2>
    </x-slot>

    <div class="px-4 sm:px-4 sm:rounded-lg">
        <p>
            Strings that are names but are also in the dictionary as words on their own.
        </p>
        <x-link href="/admin/dictionaryNames/create">Add name that is in dictionary</x-link>
        <ul>
            @foreach ($dictionaryNames as $dictionaryName)
                <li>
                    <form method="POST" action="{{ route('dictionaryNames.destroy', $dictionaryName->id) }}">
                        @method('DELETE')
                        @csrf
                            <x-link href="{{ url('admin/dictionaryNames/' . $dictionaryName->id . '/edit') }}">{{ $dictionaryName->word }}</x-link>
                            <x-small-submit-button class="ml-2">
                                {{ 'X' }}
                            </x-small-submit-button>
                    </form>
                </li>
            @endforeach
        </ul>
    </div>

</x-app-layout>
