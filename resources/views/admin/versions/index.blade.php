<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Versions
        </h2>
    </x-slot>

    <div class="sm:px-0 lg:px-0 space-y-6">
        <div class="px-4 sm:px-4 pt-0 sm:pt-0 sm:rounded-lg">
            <x-link href="/admin/versions/create">Add version</x-link>
            <ul>
                @foreach ($versions as $version)
                    <li>
                        <form method="POST" action="{{ route('versions.destroy', $version->id) }}">
                            @method('DELETE')
                            @csrf
                                <x-link href="{{ url('admin/versions/' . $version->id . '/edit') }}">{{ $version->version }}</x-link>
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
