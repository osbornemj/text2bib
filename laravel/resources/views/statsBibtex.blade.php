<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('BibTeX style file usage') }}
        </h2>
        <p>
            <x-link href="{{ url('statistics') }}">Main statistics page</x-link>
        </p>
    </x-slot>

    <div class="mx-4 mt-2 font-semibold">
        <p>
            Each row gives the name of a BibTeX style file and the number of users who have specified that file when performing one or more conversions.
        </p>
    </div>

    <div class="mx-4 mt-2 flex justify-center">
        <p>
            {{ $bsts->count() }} bst files found
        </p>
    </div>

    <div class="mx-4 mt-2 flex justify-center">
        <table class="table-auto">
            <thead>
              <tr>
                <th class="px-2">bst filename</th>
                <th class="px-2">User count</th>
              </tr>
            </thead>
            <tbody>
                @foreach ($bsts as $bst)
                <tr>
                    <td class="px-2">
                        <livewire:bst-modal :bst="$bst"/>
                    </td>
                    <td class="px-2 text-center">{{ number_format($bst->user_count) }}</td>
                </tr>
                @endforeach
                
            </tbody>
        </table>
        
    </div>

 </x-app-layout>