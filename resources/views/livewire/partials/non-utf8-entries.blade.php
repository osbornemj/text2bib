<div>
    <h2 class="font-semibold text-xl leading-tight my-4">
        {{ __('Encoding error') }}
    </h2>

    <div class="sm:px-4 lg:px-4 space-y-6">
        <div class="sm:p-0 pt-0 sm:pt-0">
            <p>
                The following {{ count($nonUtf8Entries) > 1 ? 'items contain' : 'item contains' }} at least one character that is not valid utf-8.  One way to fix the problem is to open the file in Notepad++, click on Encoding, check that it is set to UTF-8, and locate and replace the bad characters.
            </p>
            <ul class="mt-4 mb-4">
            @foreach ($nonUtf8Entries as $entry)
                <li class="ml-6 mt-4">{{ $entry }}</li>
            @endforeach
            </ul>
        </div>
    </div>

</div>

