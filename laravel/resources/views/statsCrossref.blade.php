<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('BibTeX style file usage') }}
        </h2>
        <p>
            <x-link href="{{ url('statistics') }}">Main statistics page</x-link>
        </p>
    </x-slot>

    <div class="mx-4 mt-2 flex justify-center">
        <table class="table-auto">
            <tbody>
                <thead>
                    <tr>
                      <th class="px-2"></th>
                      <th class="px-2"># conversions</th>
                    </tr>
                  </thead>
                  @foreach ($data as $datum) 
                  <tr>
                    <td class="px-2">
                        @if ($datum->use_crossref)
                            Use Crossref
                        @else
                            Do not use Crossref
                        @endif
                    </td>
                    <td class="px-2 text-center">{{ number_format($datum->crossref_count) }}</td>
                </tr>
              @endforeach
            </tbody>
        </table>
    </div>



 </x-app-layout>