<x-app-layout>

    <div class="px-4 sm:px-4 lg:px-4">
        <p class="my-4">
            This site allows you to convert a text file of references to a BibTeX file.
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
        <p class="mb-4">
            (where <code><span class="bg-slate-300 dark:bg-gray-500 font-bold">&larr;</span></code> is a line-ending character) <i>or</i> has one reference on each line, like this
        </p>
        <div class="m-5 ml-8">
            <p>
            <code>Arrow, K. J., L. Hurwicz, and H. Uzawa (1961), "Constraint qualifications in maximization problems," Naval Research Logistics Quarterly 8, 175-191.<span class="bg-slate-300 dark:bg-gray-500 font-bold">&larr;</span></code><br/>
            <code>Van de Hulst, H.C., 1981. Light scattering by small particles, Dover Publications, New York.<span class="bg-slate-300 dark:bg-gray-500 font-bold">&larr;</span></code>
        </p>
        </div>
        <p class="mb-4">
            You receive a BibTeX file of the references.
        </p>
        <p class="mb-4">
            The script attempts to detect items of the following types: <code>article</code>, <code>book</code>, <code>incollection</code>, <code>inproceedings</code>, <code>mastersthesis</code>, <code>online</code>, <code>phdthesis</code>, <code>techreport</code>, <code>unpublished</code>.  It attempts to parse the references into one of these types, regardless of their format. 
        </p>
        <p class="mb-4">
            The system does not extract the references from a file that contains other material, so the file you upload should contain <i>only</i> the list of references, not other text.  However,  each reference may be preceded by one of the strings <code>\bibitem{}</code>, <code>\bibitem{&lt;label&gt;}</code>, <code>\bibitem[&lt;text&gt;]{&lt;label&gt;}</code>, <code>\noindent</code>, <code>\smallskip</code>, <code>\bigskip</code>, or <code>\item</code> or any digit or any of the characters <code>.</code>, <code>[</code>, <code>]</code>, <code>(</code>, or <code>)</code>.
        </p>
        @if (!$user)
            <p class="mb-4">
                To get started, <x-link href="{{ url('register') }}">register</x-link> if you do not have an account, or <x-link href="{{ url('login') }}">log</x-link> in if you do.
            </p>
        @endif
    </div>

</x-app-layout>