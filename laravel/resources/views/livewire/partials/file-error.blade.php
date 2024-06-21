<div>
    <h2 class="font-semibold text-xl leading-tight my-4">
        {{ __('Source file format is incorrect') }}
    </h2>

    <div class="px-4 space-y-6">
        <div class="sm:p-0 pt-0">
            <p>
                The file you uploaded appears to be a 
                @if ($fileError == 'bibtex')
                    BibTeX file.
                @elseif ($fileError == 'bbl-natbib')
                    bbl file created by natbib.
                @endif
                This system converts text references to BibTeX format.  It does not process 
                @if ($fileError == 'bibtex')
                    BibTeX files.
                @elseif ($fileError == 'bbl-natbib')
                    bbl files created by natbib.
                @endif
                <x-link href="https://youtu.be/uDYHszzWhfk" target="_blank">This video</x-link> explains how to use the system.
            </p>
        </div>
    </div>

</div>

