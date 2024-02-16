<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            von Names
        </h2>
    </x-slot>

    <div class="sm:px-0 lg:px-0 space-y-6">
        <div class="px-4 sm:px-4 pt-0 sm:pt-0 shadow sm:rounded-lg">
            <a href="/admin/vonNames/create">Add von name</a>
            <ul>
                @foreach ($vonNames as $vonName)
                    <li>
                        <form method="POST" action="{{ route('vonNames.destroy', $vonName->id) }}">
                            @method('DELETE')
                            @csrf
                                <x-link href="{{ url('admin/vonNames/' . $vonName->id . '/edit') }}">{{ $vonName->name }}</x-link>
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
