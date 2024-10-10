<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            BibTeX  style files (bst files)
        </h2>
    </x-slot>

    <div class="ml-4 mr-2">
        <p>
            To use BibTeX in a LaTeX document, a bibliography style file (<code>bst</code> file) has to be selected and included as the argument of a <code>\bibliographystyle</code> command.
        </p>
        <p class="mt-2">
            <code>bst</code> files differ in their features, and the output produced by the conversion algorithm depends on the <code>bst</code> file specified when the source file is uploaded.
        </p>
        <p class="mt-2">
            This page lists the <code>bst</code> files about which I currently have information.  As additional <code>bst</code> files are entered by users in the file-upload form, I will investigate their properties (e.g. the fields they support) and add them to this list.  (If you enter a <code>bst</code> file not on this list, the conversion algorithm assumes some default properties for it.)
        </p>
        <p class="mt-2">
            When you click on the name of a file in this list below, its properties that are relevant to the conversion algorithm and the output it generates for the following sample document and <code>bib</code> file are shown.
        </p>
        <p class="mt-0">
            <div x-data="{ open: false }">
                <x-button-link x-on:click="open = ! open" class="text-blue-900 dark:text-blue-100">sample document</x-button-link>
                <div x-show="open" class="mx-4">
                    <p>
                        Here is the LaTeX code for the sample document, in this case using the <code>mla</code> style (with <code>natbib</code>).
                    </p>
                    <p class="mt-2">
                        <img src="/images/document.png" class="w-full md:w-4/5">
                    </p>
                </div>
            </div>
        </p>
        <p>
            <div x-data="{ open: false }">
                <x-button-link x-on:click="open = ! open">bib file used in sample document</x-button-link>
                <div x-show="open" class="mx-4">
                    <p>
                        The <code>\marginpar</code> code in the <code>title</code> field for each entry produces the text in the right margin of the sample document indicating the type of each item.  It would not, of course, be included in a real bib file.
                    </p>
                    <p class="mt-2">
                        <img src="/images/test.bib.png" class="w-full md:w-4/5">
                    </p>
                </div>
            </div>
        </p>
    </div>

    <div class="mx-4 mt-4">
        <h2 class="font-semibold text-lg leading-tight">bst files</h2>
        <div class="border-2 border-slate-500 cark:border-slate-100 p-2 m-1 mt-2">
            <p>
                @include('partials.bstSearchForm')
            </p>
        </div>
        <p>
            {{ $bsts->total()}} found
        </p>
        @if ($bsts->total())
            <p class="mt-2 mb-1">
                Click on the name of a file to see properties of the style (<span class="positive">field</span> = supported, <span class="negative">field</span> = unsupported) and the sample document generated using that style.
            </p>
            <div class="sm:rounded-lg">
                @if ($bsts->count())
                    <ul>
                        @foreach ($bsts as $bst)
                        <li>
                            <div x-data="{ open: false }">
                                <x-button-link x-on:click="open = ! open">{{ $bst->name }}</x-button-link>
                                <div x-show="open" class="mb-2">
                                    <div class="mb-0">
                                        @include('index.partials.bstProperties')
                                    </div>
                                    @if ($bst->available && $bst->checked)
                                        <img src="/images/{{ $bst->name }}-sample.png" class="w-full md:w-4/5">
                                    @endif
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                @endif

                {{ $bsts->links() }}
            </div>
        @endif
    </div>

</x-app-layout>
