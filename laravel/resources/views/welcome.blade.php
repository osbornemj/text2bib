<x-app-layout>

    <div class="px-4">

        {{--
        <div class="bg-blue-200 text-blue-800 m-4 p-2">
            This site will be down from around 16:00 to 20:00 UTC on 2025.2.1 for maintenance.
        </div>
        --}}
        
        <p class="my-4">
            This site allows you to convert a text file of references to a <x-link href="https://economics.utoronto.ca/osborne/latex/BIBTEX.HTM" target="_blank">BibTeX file</x-link>.  It is maintained by <x-link href="https://economics.utoronto.ca/osborne" target="_blank">Martin J. Osborne</x-link>.
        </p>
        @if (!$user)
            <p class="mb-4">
                To get started, <x-link href="{{ url('register') }}">register</x-link> if you do not have an account, or <x-link href="{{ url('login') }}">log</x-link> in if you do.
            </p>
        @endif

        <p class="mb-4">
            Explanatory video: <x-link href="https://youtu.be/uDYHszzWhfk" target="_blank">https://youtu.be/uDYHszzWhfk</x-link> (5:45).  Retrieving items from <x-link href="https://crossref.org" target="_blank">crossref.org</x-link> during conversion: <x-link href="https://youtu.be/W4-WEwo2esY" target="_blank">https://youtu.be/W4-WEwo2esY</x-link> (5:56).
        </p>

        <p class="mb-4">
            You upload a <b>plain text</b> file using the <b>utf-8</b> encoding that <i>either</i> has the references separated by blank lines, like this
        </p>
        <div>
            <p class="m-4 ml-8">
                <code>Arrow, K. J., L. Hurwicz, and H. Uzawa (1961), "Constraint<span class="bg-slate-300 dark:bg-gray-500 font-bold">&larr;</span><br/>
                qualifications in maximization problems," Naval Research Logistics<span class="bg-slate-300 dark:bg-gray-500 font-bold">&larr;</span><br/>
                Quarterly 8, 175-191.<span class="bg-slate-300 dark:bg-gray-500 font-bold">&larr;</span></code><br/>
                <code><span class="bg-slate-300 dark:bg-gray-500 font-bold">&larr;</span></code><br/>
                <code>Van de Hulst, H.C., 1981. Light Scattering by small particles, Dover<span class="bg-slate-300 dark:bg-gray-500 font-bold">&larr;</span><br/>
                Publications, New York.<span class="bg-slate-300 dark:bg-gray-500 font-bold">&larr;</span></code>
        </p>
        </div>
        <p class="mb-4 ml-8">
            (where <code><span class="bg-slate-300 dark:bg-gray-500 font-bold">&larr;</span></code> is a line-ending character)
        </p>
        <p class="mb-4">
            <i>or</i> has one reference on each line, like this
        </p>
        <div class="m-5 ml-8">
            <p>
            <code>Arrow, K. J., L. Hurwicz, and H. Uzawa (1961), "Constraint qualifications in maximization problems," Naval Research Logistics Quarterly 8, 175-191.<span class="bg-slate-300 dark:bg-gray-500 font-bold">&larr;</span></code><br/>
            <code>Van de Hulst, H.C., 1981. Light scattering by small particles, Dover Publications, New York.<span class="bg-slate-300 dark:bg-gray-500 font-bold">&larr;</span></code>
            </p>
        </div>
        <p class="mb-4">
            <i>or</i> starts with <code>&lt;li&gt;</code> and has each subsequent reference preceded by <code>&lt;li&gt;</code>, like this
        </p>
        <div class="m-5 ml-8">
            <p>
            <code>&lt;li&gt;Arrow, K. J., L. Hurwicz, and H. Uzawa (1961), "Constraint qualifications in maximization problems," Naval Research Logistics Quarterly 8, 175-191. 
            &lt;li&gt;Van de Hulst, H.C., 1981. Light scattering by small particles, Dover Publications, New York.<span class="bg-slate-300 dark:bg-gray-500 font-bold"></span></code>
            </p>
        </div>
        <p class="mb-4">
            You receive a <x-link href="https://economics.utoronto.ca/osborne/latex/BIBTEX.HTM" target="_blank">BibTeX file</x-link> of the references.
        </p>
        <p class="mb-4">
            Each reference in the file you upload may start with the authors, as in the examples above, or with the year, like this
        </p>
        <div class="m-5 ml-8">
            <p>
            <code>1961 Arrow, K. J., L. Hurwicz, and H. Uzawa, "Constraint qualifications in maximization problems," Naval Research Logistics Quarterly 8, 175-191.<span class="bg-slate-300 dark:bg-gray-500 font-bold"></span></code>
            </p>
        </div>
        <p class="mb-4">
            Each reference may be preceded by one of the strings <code>\bibitem{}</code>, <code>\bibitem{&lt;label&gt;}</code>, <code>\bibitem[&lt;text&gt;]{&lt;label&gt;}</code>, <code>\noindent</code>, <code>\smallskip</code>, <code>\bigskip</code>, or <code>\item</code> or any of the characters in the string <code>.,[]()|*+</code>, or, if it doesn't start with the year, any digit.  If it starts with a string in brackets that contains at least one letter (e.g. <code>[Arrow1990]</code>), the string is interpreted as the label for the item.  (An entirely numeric string in brackets is intepreted as a number for the item, and is ignored.)  If it starts with the year, the year may be followed by any character in the string <code>|*+</code>.
        </p>
        <p class="mb-4">
            The script attempts to detect items of the following types: <code>article</code>, <code>book</code>, <code>incollection</code>, <code>inproceedings</code>, <code>mastersthesis</code>, <code>online</code>, <code>phdthesis</code>, <code>techreport</code>, <code>unpublished</code>.  It attempts to parse the references into one of these types regardless of their format. 
        </p>
        <p class="mb-4">
            The system does not extract the references from a file that contains other material, so the file you upload should contain <i>only</i> the list of references, not other text.  
        </p>
    </div>

</x-app-layout>