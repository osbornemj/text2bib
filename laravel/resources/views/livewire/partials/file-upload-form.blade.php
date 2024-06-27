<div>

    <h2 class="font-semibold text-xl leading-tight mt-4">
        {{ __('Convert from text to BibTeX') }}
    </h2>
    <p>
        <div class="text-sm">
            Algorithm version {{ $version }}
        </div>
    </p>

    @if ($conversionCount)
        <div class="space-y-6 mt-4">
            <x-link href="{{ url('conversions') }}">Your previous conversions</x-link>
        </div>
    @endif

    {{--
    <div class="bg-emerald-100 dark:bg-emerald-600 mt-2">
        2024.4.30: A coding error introduced three days ago has generated many conversion errors in items whose titles contain punctuation.  I have now fixed the error.
    </div>
    --}}
    
    <div class="space-y-6 my-4">
        <div class="sm:pt-0">
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
                    Each reference must start with either the authors or the year, and if it starts with the year, the authors must come next.  In addition, each reference must include a title.
                </li>
                <li>
                    Your file must be plain text (txt), with character encoding utf-8.  (If the character encoding is not utf-8, the system will attempt to convert it to utf-8, but may not do so accurately.)
                </li>
                <li>
                    The size of your file must be at most 100K.
                </li>
                <li>
                    The file you upload will be visible to the administrator of the site and potentially to other users.  Do not upload a file that contains personal information.
                </li>
            </ul>
        </div>
    </div>

    <form method="POST" accept="txt" wire:submit="submit" onsubmit="window.scrollTo(0, 0);" enctype="multipart/form-data" class="mt-0 space-y-0">
    @csrf

    <div>
        <x-text-input id="file" class="block mt-1 max-w-xl w-full" type="file" name="file" wire:model="uploadForm.file" accept="txt"
            required autofocus />
        <x-input-error :messages="$errors->get('uploadForm.file')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="use" :value="__('How do you plan to use the BibTeX file that the system creates?')" class="mt-4 mb-1"/>

        @foreach ($useOptions as $key => $option)
            <x-radio-input wire:model="uploadForm.use" value="{{ $key }}" class="peer/{{ $key }}" /> 
            <x-value-label for="{{ $key }}" class="peer-checked/{{ $key }}:text-blue-600 ml-1" :value="$option" />
            <br/>
        @endforeach

        <x-input-error :messages="$errors->get('uploadForm.use')" class="mt-2" />
            
        <div class="hidden peer-checked/other:block">
            <x-text-input class="w-full" wire:model="uploadForm.other_use" maxlength="255" />
        </div>

        <x-input-error :messages="$errors->get('uploadForm.other_use')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="item_separator" :value="__('Item separator')" class="mt-4 mb-1"/>
    
        <x-radio-input wire:model="uploadForm.item_separator" value="line" class="peer/line" /> 
        <x-value-label for="line" class="peer-checked/line:text-blue-600 ml-1" :value="__('Blank line')" />

        <x-radio-input wire:model="uploadForm.item_separator" value="cr" class="peer/cr ml-4" />
        <x-value-label for="cr" class="peer-checked/cr:text-blue-600 ml-1" :value="__('Carriage return')" />

        <x-input-error :messages="$errors->get('uploadForm.item_separator')" class="mt-2" />

        <x-option-info class="peer-checked/line:block">
            The items in your file are separated by blank lines.  Carriage returns within items will be treated as spaces.
        </x-option-info>
        <x-option-info class="peer-checked/cr:block">
            Every line in your file is a separate item.
        </x-option-info>
    </div>

    <div>
        <x-input-label for="language" :value="__('Language of auxiliary words in references')" class="mt-4 mb-1"/>
    
        <x-radio-input wire:model="uploadForm.language" value="en" class="peer/en" /> 
        <x-value-label for="en" class="peer-checked/en:text-blue-600 ml-1" :value="__('English')" />

        <x-radio-input wire:model="uploadForm.language" value="my" class="peer/my ml-4" /> 
        <x-value-label for="en" class="peer-checked/my:text-blue-600 ml-1" :value="__('Burmese')" />

        <x-radio-input wire:model="uploadForm.language" value="cz" class="peer/cz ml-4" />
        <x-value-label for="cz" class="peer-checked/cz:text-blue-600 ml-1" :value="__('Czech')" />

        <x-radio-input wire:model="uploadForm.language" value="nl" class="peer/nl ml-4" />
        <x-value-label for="nl" class="peer-checked/nl:text-blue-600 ml-1" :value="__('Dutch')" />

        <x-radio-input wire:model="uploadForm.language" value="fr" class="peer/fr ml-4" />
        <x-value-label for="fr" class="peer-checked/fr:text-blue-600 ml-1" :value="__('French')" />

        <x-radio-input wire:model="uploadForm.language" value="pt" class="peer/pt ml-4" />
        <x-value-label for="pt" class="peer-checked/pt:text-blue-600 ml-1" :value="__('Portuguese')" />

        <x-radio-input wire:model="uploadForm.language" value="es" class="peer/es ml-4" />
        <x-value-label for="es" class="peer-checked/es:text-blue-600 ml-1" :value="__('Spanish')" />

        <x-input-error :messages="$errors->get('uploadForm.language')" class="mt-2" />

        <div class="mt-2 dark:text-gray-300">
            Select the language of the <b>auxiliary</b> words in your references, like the month of publication and the words meaning "retrieved from" for a webpage.  The language of the reference itself (e.g. the title) is not relevant to this setting.  If you choose a setting other than English, accented letters will not be converted to TeX, regardless of the value you choose for that setting (which should cause no issues for modern TeX systems).  Even if you select English, words for "and" between authors' name in some languages other than English will be handled correctly.  (<i>If words are not recognized, or you want a language added, tell me in the Comments.</i>)
        </div>
    </div>

    <div>
        <x-input-label for="label_style" :value="__('Label style')" class="mt-4 mb-1" />
    
        <x-radio-input name="label_style" wire:model="uploadForm.label_style" value="short" class="peer/short" /> 
        <x-value-label for="short" class="peer-checked/short:text-blue-600 ml-1" :value="__('Short')" />

        <x-radio-input name="label_style" wire:model="uploadForm.label_style" value="long" class="peer/long ml-4" />
        <x-value-label for="long" class="peer-checked/long:text-blue-600 ml-1" :value="__('Long')" />

        <x-radio-input name="label_style" wire:model="uploadForm.label_style" value="gs" class="peer/gs ml-4" />
        <x-value-label for="gs" class="peer-checked/gs:text-blue-600 ml-1" :value="__('Google Scholar')" />

        <x-input-error :messages="$errors->get('uploadForm.label_style')" class="mt-2" />

        <x-option-info class="peer-checked/short:block">
            Concatenation of lowercased first letter of each author's last name and last two digits of year.  E.g. Arrow and Hahn (1971) &rArr; ah71
        </x-option-info>
        <x-option-info class="peer-checked/long:block">
            Concatenation of authors' last names and year.  E.g. Arrow and Hahn (1971)  &rArr; ArrowHahn1971
        </x-option-info>
        <x-option-info class="peer-checked/gs:block">
            Concatenation of lowercased first author's last name, year of publication, and lowercased significant first word of title, where "a", "an", "the", and "on" are regarded as insignificant. (I don't know exactly which words Google Scholar treats as insignificant. If you know, please tell me.) If the first author's last name contains a space, my algorithm uses the last segment, whereas Google Scholar may use the first segment.  E.g. Arrow and Hahn (1971) &rArr; arrow1971general
        </x-option-info>
    </div>

    <div>
        <x-input-label for="override_labels" :value="__('Override labels in file (if any)?')" class="mt-4 mb-1" />
    
        <x-radio-input wire:model="uploadForm.override_labels" value="1" class="peer/1" /> 
        <x-value-label for="1" class="peer-checked/1:text-blue-600 ml-1" :value="__('Yes')" />

        <x-radio-input wire:model="uploadForm.override_labels" value="0" class="peer/0 ml-4" />
        <x-value-label for="0" class="peer-checked/0:text-blue-600 ml-1" :value="__('No')" />

        <x-input-error :messages="$errors->get('uploadForm.override_labels')" class="mt-2" />

        <x-option-info class="peer-checked/1:block">
            For any item in your file that starts with <code>\bibitem{&lt;label&gt;}</code>, where <code>&lt;label&gt;</code> is a string, that string is ignored and a label is constructed according to the option you have selected.
        </x-option-info>
        <x-option-info class="peer-checked/0:block">
            For any item in your file that starts with <code>\bibitem{&lt;label&gt;}</code>, where <code>&lt;label&gt;</code> is a string, that string is used as the label for the item; the label style you have selected is ignored for this item. 
        </x-option-info>
    </div>

    <div>
        <x-input-label for="line_endings" :value="__('Line-ending style for generated bibtex.bib file')" class="mt-4 mb-1" />
    
        <x-radio-input wire:model="uploadForm.line_endings" value="w" class="peer/w" /> 
        <x-value-label for="w" class="peer-checked/w:text-blue-600 ml-1" :value="__('Windows')" />

        <x-radio-input wire:model="uploadForm.line_endings" value="l" class="peer/l ml-4" />
        <x-value-label for="l" class="peer-checked/l:text-blue-600 ml-1" :value="__('Linux')" />

        <x-input-error :messages="$errors->get('uploadForm.line_endings')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="char_encoding" :value="__('Convert accented characters to TeX?')" class="mt-4 mb-1" />
    
        <x-radio-input wire:model="uploadForm.char_encoding" value="utf8leave" class="peer/utf8leave" />
        <x-value-label for="utf8leave" class="peer-checked/utf8leave:text-blue-600 ml-1" :value="__('No')" />

        <x-radio-input wire:model="uploadForm.char_encoding" value="utf8" class="peer/utf8 ml-4" /> 
        <x-value-label for="utf8" class="peer-checked/utf8:text-blue-600 ml-1" :value="__('Yes')" />

        <x-input-error :messages="$errors->get('uploadForm.char_encoding')" class="mt-2" />

        <x-option-info class="peer-checked/utf8leave:block">
            In many modern TeX systems, you can safely choose this setting.
        </x-option-info>
        <x-option-info class="peer-checked/utf8:block">
            Many, though not all, accented letters will be translated to TeX.  For example, é will be translated to {\'e}.  Use this setting if your TeX system does not handle UTF-8.  (If you want me to make additions to the list of characters that are translated, post a comment.)
        </x-option-info>
    </div>

    <div>
        <x-input-label for="percent_comment" :value="__('Treat % as starting a comment?')" class="mt-4 mb-1" />
    
        <x-radio-input wire:model="uploadForm.percent_comment" value="1" class="peer/1" /> 
        <x-value-label for="1" class="peer-checked/1:text-blue-600 ml-1" :value="__('Yes')" />

        <x-radio-input wire:model="uploadForm.percent_comment" value="0" class="peer/0 ml-4" />
        <x-value-label for="0" class="peer-checked/0:text-blue-600 ml-1" :value="__('No')" />

        <x-input-error :messages="$errors->get('uploadForm.percent_comment')" class="mt-2" />

        <x-option-info class="peer-checked/1:block">
            Every "%" not preceded by "\" and the characters that follow up to the end of the line will be removed before the item is processed.
        </x-option-info>
        <x-option-info class="peer-checked/0:block">
            Every "%" will be treated as a regular character.
        </x-option-info>
    </div>

    <div>
        <x-input-label for="include_source" :value="__('Include each reference as comment above entry in BibTeX file?')" class="mt-4 mb-1" />
    
        <x-radio-input wire:model="uploadForm.include_source" value="1" class="peer/1" /> 
        <x-value-label for="1" class="peer-checked/1:text-blue-600 ml-1" :value="__('Yes')" />

        <x-radio-input wire:model="uploadForm.include_source" value="0" class="peer/0 ml-4" />
        <x-value-label for="0" class="peer-checked/0:text-blue-600 ml-1" :value="__('No')" />

        <x-input-error :messages="$errors->get('uploadForm.include_source')" class="mt-2" />

        <x-option-info class="peer-checked/1:block">
            The original reference will be included as a comment above each item in the BibTeX file that the system creates (making it easier for you to check the correctness of the conversion).
        </x-option-info>
        <x-option-info class="peer-checked/0:block">
            The original references will not be included in the BibTeX file.
        </x-option-info>
    </div>

    <div>
        <x-input-label for="report_type" :value="__('Report type')"  class="mt-4 mb-1"/>
    
        <x-radio-input wire:model="uploadForm.report_type" value="standard" class="peer/standard" /> 
        <x-value-label for="standard" class="peer-checked/standard:text-blue-600 ml-1" :value="__('Standard')" />

        <x-radio-input wire:model="uploadForm.report_type" value="detailed" class="peer/detailed ml-4" />
        <x-value-label for="detailed" class="peer-checked/detailed:text-blue-600 ml-1" :value="__('Detailed')" />

        <x-input-error :messages="$errors->get('uploadForm.report_type')" class="mt-2" />

        <x-option-info class="peer-checked/standard:block">
            Standard information is provided about the conversion of each item.
        </x-option-info>
        <x-option-info class="peer-checked/detailed:block">
            Detailed information is included about the logic behind the conversion.  This setting is useful only if you want to suggest code changes to improve the conversion. 
        </x-option-info>
    </div>

    <div>
        <x-input-label for="save_settings" :value="__('Save settings as default for future conversions?')" class="mt-4 mb-1" />

        <x-radio-input wire:model="uploadForm.save_settings" value="1" class="peer/1" /> 
        <x-value-label for="1" class="peer-checked/1:text-blue-600 ml-1" :value="__('Yes')" />

        <x-radio-input wire:model="uploadForm.save_settings" value="0" class="peer/0 ml-4" />
        <x-value-label for="0" class="peer-checked/0:text-blue-600 ml-1" :value="__('No')" />

        <x-input-error :messages="$errors->get('uploadForm.save_settings')" class="mt-2" />
        </div>

    <div class="pt-4">
        <x-primary-button class="ml-0">
            {{ __('Submit') }}
        </x-primary-button>
    </div>

    </form>
</div>
