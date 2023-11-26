<x-guest-layout>
    @if (Route::has('login'))
        <div class="sm:fixed sm:top-0 sm:right-0 p-6 text-right z-10">
            @auth
                <a href="{{ url('/dashboard') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Dashboard</a>
            @endauth
        </div>
    @endif

    <div class="px-4 sm:px-4 lg:px-4">
        <p class="my-4">
            This site allows you to convert a file of references to a BibTeX file.
        </p>
        <p class="mb-4">
            You upload a <b>plain text</b> file that <i>either</i> has the references separated by blank lines, like this
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
            The script attempts to parse the references regardless of their format.
        </p>
        <p class="mb-4">
            The system does not extract the references from a file, so the file you upload should contain only the list of references, not other text.  However,  each reference may be preceded by one of the strings <code>\bibitem{}</code>, <code>\bibitem[&lt;label&gt;]{}</code>, <code>\noindent</code>, <code>\smallskip</code>, <code>\bigskip</code>, or <code>\item</code> or any digit or any of the characters <code>.</code>, <code>[</code>, <code>]</code>, <code>(</code>, or <code>)</code>.
        </p>
        <p class="mb-4">
            To get started, register if you do not have an account, or log in if you do.
        </p>
    </div>

</x-guest-layout>