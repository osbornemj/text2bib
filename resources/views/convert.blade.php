<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Convert from text to BibTeX') }}
        </h2>
    </x-slot>

    <div class="sm:px-4 lg:px-4 space-y-6 mb-4">
        <div class="sm:p-0 pt-0 sm:pt-0">
            <h3 class="font-semibold text-lg leading-tight">
                {{ __('Requirements')}}
            </h3>
                <ul class="list-disc list-outside ml-8">
                <li>
                    Your file must contain only a list of references, not any other text.  (The conversion routine does not extract references from text.)
                </li>
                <li>
                    <b>Either</b> each reference in your file must be on a separate line <b>or</b> the references must be separated by blank lines.
                </li>
                <li>
                    Your file must be plain text (txt), with character encoding utf-8.
                </li>
                <li>
                    The size of your file must be at most 100K.
                </li>
            </ul>
        </div>
    </div>

        <div class="sm:px-4 lg:px-4 space-y-6">
            <div class="sm:p-0 pt-0 sm:pt-0">
                <livewire:convert-file />
            </div>
        </div>

</x-app-layout>
