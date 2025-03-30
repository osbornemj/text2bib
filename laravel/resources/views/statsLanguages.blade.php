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
            Each row gives a language and the number of users who have specified that language when performing one or more conversions.
        </p>
    </div>

    <div class="mx-4 mt-2 flex justify-center">
        <p>
            {{ $data->count() }} languages found
        </p>
    </div>

    <div class="mx-4 mt-2 flex justify-center">
        <table class="table-auto">
            <thead>
              <tr>
                <th class="px-2">Language</th>
                <th class="px-2">User count</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($data as $datum) 
                <tr>
                    <td class="px-2">
                        {{ $datum->language }}
                    </td>
                    <td class="px-2 text-center">{{ $datum->user_count }}</td>
                </tr>
              @endforeach
            </tbody>
        </table>
    </div>



 </x-app-layout>