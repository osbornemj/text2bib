<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('BibTeX style file usage') }}
        </h2>
    </x-slot>

    <div class="mx-4 mt-2 font-semibold">
        <p>
            The number in each row is the number of users who have specified that BibTeX style file when performing one or more conversions.
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
                <td class="px-2">{{ $bst->name }}</td>
                <td class="px-2 text-center">{{ $bst->user_count }}</td>
              </tr>
              @endforeach
            </tbody>
        </table>
    </div>



 </x-app-layout>