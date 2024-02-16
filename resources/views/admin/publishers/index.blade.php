<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Publishers
        </h2>
    </x-slot>

    <div class="sm:px-0 lg:px-0 space-y-6">
        <div class="px-4 sm:px-4 pt-0 sm:pt-0 sm:rounded-lg">
            <x-link href="/admin/publishers/create">Add publisher</x-link>
            <ul class="mt-4">
                @foreach ($publishers as $publisher)
                    <li>
                        <form method="POST" action="{{ route('publishers.destroy', $publisher->id) }}">
                            @method('DELETE')
                            @csrf
                                <x-link href="{{ url('admin/publishers/' . $publisher->id . '/edit') }}">{{ $publisher->name }}</x-link>
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
