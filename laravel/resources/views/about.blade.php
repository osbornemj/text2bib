<x-app-layout>

    <div class="mt-6 px-4 sm:px-4 lg:px-4">
        <x-paragraph class="mt-4">
            This project was initiated by Martin J. Osborne in 2006 when he was the first Managing Editor of the (Open Access) journal <x-link href="https://econtheory.org" target="_blank">Theoretical Economics</x-link>.  (He was a member of the independent group that founded the journal, which was later taken over by the Econometric Society.)  The  purpose of the project was to convert into BibTeX the plain text references in accepted articles so that they could easily be consistently formatted.
        </x-paragraph>
        <x-paragraph class="mt-4">
            With funding from the Student Experience Program of <x-link href="http://open.utoronto.ca/" target="_blank">Project Open Source | Open Access</x-link> at the University of Toronto, <x-link href="https://ca.linkedin.com/in/fabian-bai-4545ab2b" target="_blank">Fabian Qifei Bai</x-link> created the first version of the conversion script in the Spring of 2007.
        </x-paragraph>
        <x-paragraph class="mt-4">
            When Bai graduated from the University of Toronto later in 2007, Osborne took over the coding and wrote a front end for public use, using the Open Journals System of the <x-link href="https://pkp.sfu.ca/" target="_blank">Public Knowledge Project</x-link> as a framework.  He has continued to develop both the conversion engine and the front end since then.
        </x-paragraph>
        <x-paragraph class="mt-4">
            Starting in the summer of 2023, Osborne reimplemented the system using the <x-link href="https://laravel.com" target="_blank">Laravel</x-link> framework, at the same time making many improvements in the conversion engine.  The code is available on GitHub.
        </x-paragraph>
        <x-paragraph class="mt-4">
            While the current version of the system has many improvements relative to the previous one, a few little-used features of the previous system are not currently supported.  For example, the script no longer attempts to support languages other than English (the support in the previous version was very incomplete) and only the utf-8 character encoding is supported.
        </x-paragraph>
        <x-paragraph class="mt-4">
            The source code is available on Github: <x-link href="https://github.com/osbornemj/text2bib">https://github.com/osbornemj/text2bib</x-link>.
        </x-paragraph>

    </div>

</x-app-layout>