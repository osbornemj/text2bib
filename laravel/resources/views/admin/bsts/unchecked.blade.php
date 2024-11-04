<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Unchecked BibTeX style files (bst files)
        </h2>
        {{ $uncheckedBsts->total()}} found
    </x-slot>

    <div class="px-4 sm:px-4 sm:rounded-lg">
        <div class="mb-4">
            <x-link href="/admin/bsts">Checked</x-link>
        </div>

        @if ($uncheckedBsts->count())
            <ul>
                @foreach ($uncheckedBsts as $bst)
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

        {{ $uncheckedBsts->links() }}
    </div>

</x-app-layout>
