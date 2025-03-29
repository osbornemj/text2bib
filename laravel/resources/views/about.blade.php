<x-app-layout>

    <div class="mt-6 px-4">
        <x-paragraph class="mt-4">
            I (<x-link href="https://economics.utoronto.ca/osborne" target="_blank">Martin J. Osborne</x-link>) initiated this project in 2006 when I was the first Managing Editor of the (Open Access) journal <x-link href="https://econtheory.org" target="_blank">Theoretical Economics</x-link>.  (I was a member of the independent group that founded the journal, which was later taken over by the Econometric Society.)  The  purpose of the project was to convert into BibTeX the plain text references in accepted articles so that they could easily be consistently formatted.
        </x-paragraph>
        <x-paragraph class="mt-4">
            With funding from the Student Experience Program of <x-link href="http://open.utoronto.ca/" target="_blank">Project Open Source | Open Access</x-link> at the University of Toronto, <x-link href="https://ca.linkedin.com/in/fabian-bai-4545ab2b" target="_blank">Fabian Qifei Bai</x-link> created the first version of the conversion script in the Spring of 2007.
        </x-paragraph>
        <x-paragraph class="mt-4">
            When Bai graduated from the University of Toronto later in 2007, I took over the coding and wrote a front end for public use, using the Open Journals System of the <x-link href="https://pkp.sfu.ca/" target="_blank">Public Knowledge Project</x-link> as a framework.  I have continued to develop both the conversion engine and the front end since then.
        </x-paragraph>
        <x-paragraph class="mt-4">
            Starting in the summer of 2023, I reimplemented the system using the <x-link href="https://laravel.com" target="_blank">Laravel</x-link> framework, at the same time making many improvements in the conversion engine.  The new version was released on 2024.3.15.
        </x-paragraph>
        <x-paragraph class="mt-4">
            The source code is available on Github: <x-link href="https://github.com/osbornemj/text2bib" target="_blank">https://github.com/osbornemj/text2bib</x-link>.
        </x-paragraph>
        <x-paragraph class="mt-4">
            The converter consists of a large number of hand-coded rules for extracting the author, title, and publication information from character strings that represent references.  I make improvements to it by occasionally looking for errors in the conversions for files uploaded by users.  When I see an error, I add the source and the correct version of the BibTeX entry to a database table of examples and modify the code to deal with it correctly, while still correctly converting all the other examples.  The examples table currently contains <x-link href="{{ url('examples') }}">{{ $exampleCount }} items</x-link>.  (Unfortunately error reports by users are few and far between, and almost no user responds to clarifactory questions, so the improvement of the algorithm proceeds much more slowly that it could.)
        </x-paragraph>
        <x-paragraph class="mt-4">
            A natural alternative approach is to use AI rather than a hand-coded algorithmm.  The only barrier to doing so is cost, but surely AI is where the future lies.
        </x-paragraph>

    </div>

</x-app-layout>