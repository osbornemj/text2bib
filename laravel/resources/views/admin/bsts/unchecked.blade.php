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
                    <div>
                        <x-link href="/admin/bsts/{{ $bst->id }}/edit">{{ $bst->name }}</x-link>
                    </div>
                </li>
                @endforeach
            </ul>
        @endif

        {{ $uncheckedBsts->links() }}
    </div>

</x-app-layout>
