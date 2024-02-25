<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Names
        </h2>
    </x-slot>

    <div class="sm:px-0 lg:px-0 space-y-6">
        <div class="px-4 sm:px-4 pt-0 sm:pt-0 sm:rounded-lg">
            <div class="mb-4">
                <x-link href="/admin/names/create">Add name</x-link>
            </div>
            <ul>
                @foreach ($names as $name)
                    <li>
                        <form method="POST" action="{{ route('names.destroy', $name->id) }}">
                            @method('DELETE')
                            @csrf
                                <x-link href="{{ url('admin/names/' . $name->id . '/edit') }}">{{ $name->name }}</x-link>
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
