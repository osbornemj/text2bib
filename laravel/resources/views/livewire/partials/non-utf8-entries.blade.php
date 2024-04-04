<div>
    <h2 class="font-semibold text-xl leading-tight my-4">
        {{ __('Encoding error') }}
    </h2>

    <div class="sm:px-4 lg:px-4 space-y-6">
        <div class="sm:p-0 pt-0 sm:pt-0">
            <p>
                The character encoding of the following {{ count($unknownEncodingEntries) > 1 ? 'items' : 'item' }}, according to PHP, is not UTF-8, ISO-8859-1, or Windows-1252.  (The script works with the UTF-8 encoding.  It currently converts ISO-8859-1 and Windows-1252, but not other encodings, to UTF-8.) One way to fix the problem is to open the file you uploaded in <x-link href="https://notepad-plus-plus.org/" target="_blank">Notepad++</x-link>, click on Encoding, and then click on "Convert to UTF-8".
            </p>
            <ul class="mt-4 mb-4">
            @foreach ($unknownEncodingEntries as $entry)
                <li class="ml-6 mt-4">
                    {{ $entry }}
                </li>
            @endforeach
            </ul>
        </div>
    </div>

</div>

