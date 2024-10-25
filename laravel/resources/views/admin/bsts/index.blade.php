<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            BibTeX  style files (bst files)
        </h2>
        {{ $bsts->total()}} found
    </x-slot>

    <div class="px-4 sm:px-4 sm:rounded-lg">
        <div class="mb-4">
            <x-link href="/admin/bsts/create">Add BibTeX style file</x-link>
            &nbsp;&bull;&nbsp;
            <x-link href="/admin/uncheckedBsts">Unchecked</x-link>
        </div>

        @if ($bsts->count())
            <h3 class="mt-4 font-semibold text-lg leading-tight">Checked</h3>
            <ul>
                @foreach ($bsts as $bst)
                <li>
                    <form method="POST" action="{{ route('bsts.destroy', $bst->id) }}" onsubmit="return confirm('Do you really want to delete this file?');">
                        @method('DELETE')
                        @csrf
                        <x-link href="{{ url('admin/bsts/' . $bst->id . '/edit') }}">{{ $bst->name }}</x-link>
                        @if (! $bst->available)
                            (not available)
                        @endif
                        <x-small-submit-button class="ml-2 bg-red-400 dark:bg-red-800">
                            {{ 'X' }}
                        </x-small-submit-button>
                    </form>
                </li>
                @endforeach
            </ul>
        @endif

        {{ $bsts->links() }}
    </div>

</x-app-layout>
