<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Publication cities
        </h2>
    </x-slot>

    <div class="sm:px-0 lg:px-0 space-y-6">
        <div class="px-4 sm:px-4 pt-0 sm:pt-0 sm:rounded-lg">
            <a href="/admin/cities/create">Add publication city</a>
            <ul>
                @foreach ($cities as $city)
                    <li>
                        <form method="POST" action="{{ route('cities.destroy', $city->id) }}">
                            @method('DELETE')
                            @csrf
                                <x-link href="{{ url('admin/cities/' . $city->id . '/edit') }}">{{ $city->name }}</x-link>
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
