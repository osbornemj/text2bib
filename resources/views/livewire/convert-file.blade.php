<div>

    <form method="POST" wire:submit="submit()" method="POST" accept="txt" enctype="multipart/form-data" class="mt-0 space-y-0">
        @csrf

        <div>
            <x-input-label for="file" :value="__('File')" />
            <x-text-input id="file" class="block mt-1 max-w-xl w-full" type="file" name="file" wire:model="form.file" accept="txt"
                required autofocus />
            <x-input-error :messages="$errors->get('file')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="item_separator" :value="__('Item separator')"  class="mt-4"/>
        
            <x-radio-input wire:model="form.item_separator" value="line" class="peer/line" /> 
            <x-value-label for="line" class="peer-checked/line:text-blue-600 ml-1" :value="__('Blank line')" />

            <x-radio-input wire:model="form.item_separator" value="cr" class="peer/cr ml-4" />
            <x-value-label for="cr" class="peer-checked/cr:text-blue-600 ml-1" :value="__('Carriage return')" />

            <x-input-error :messages="$errors->get('item_separator')" class="mt-2" />

            <x-option-info class="peer-checked/line:block">
                The items  in your file are separated by blank lines.  Carriage returns within items will be treated as spaces.
            </x-option-info>
            <x-option-info class="peer-checked/cr:block">
                Every line in your file is a separate item.
            </x-option-info>
        </div>

        <div>
            <x-input-label for="first_component" :value="__('First component of each item in your file')" class="mt-4" />
        
            <x-radio-input wire:model="form.first_component" value="authors" class="peer/authors" /> 
            <x-value-label for="authors" class="peer-checked/authors:text-blue-600 ml-1" :value="__('Authors')" />

            <x-radio-input wire:model="form.first_component" value="year" class="peer/year ml-4" />
            <x-value-label for="year" class="peer-checked/year:text-blue-600 ml-1" :value="__('Year')" />

            <x-input-error :messages="$errors->get('first_component')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="label_style" :value="__('Label style')" class="mt-4" />
        
            <x-radio-input name="label_style" wire:model="form.label_style" value="short" class="peer/short" /> 
            <x-value-label for="short" class="peer-checked/short:text-blue-600 ml-1" :value="__('Short')" />

            <x-radio-input name="label_style" wire:model="form.label_style" value="long" class="peer/long ml-4" />
            <x-value-label for="long" class="peer-checked/long:text-blue-600 ml-1" :value="__('Long')" />

            <x-radio-input name="label_style" wire:model="form.label_style" value="gs" class="peer/gs ml-4" />
            <x-value-label for="gs" class="peer-checked/gs:text-blue-600 ml-1" :value="__('Google Scholar')" />

            <x-input-error :messages="$errors->get('label_style')" class="mt-2" />

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
            <x-input-label for="override_labels" :value="__('Override labels in file (if any)?')" class="mt-4" />
        
            <x-radio-input wire:model="form.override_labels" value="1" class="peer/1" /> 
            <x-value-label for="1" class="peer-checked/1:text-blue-600 ml-1" :value="__('Yes')" />

            <x-radio-input wire:model="form.override_labels" value="0" class="peer/0 ml-4" />
            <x-value-label for="0" class="peer-checked/0:text-blue-600 ml-1" :value="__('No')" />

            <x-input-error :messages="$errors->get('override_labels')" class="mt-2" />

            <x-option-info class="peer-checked/1:block">
                For any item in your file that starts with <code>\bibitem{&lt;label&gt;}</code>, where <code>&lt;label&gt;</code> is a string, that string is ignored and a label is constructed according to the option you have selected.
            </x-option-info>
            <x-option-info class="peer-checked/0:block">
                For any item in your file that starts with <code>\bibitem{&lt;label&gt;}</code>, where <code>&lt;label&gt;</code> is a string, that string is used as the label for the item; the label style you have selected is ignored for this item. 
            </x-option-info>
        </div>

        <div>
            <x-input-label for="line_endings" :value="__('Line-ending style for output')" class="mt-4" />
        
            <x-radio-input wire:model="form.line_endings" value="w" class="peer/w" /> 
            <x-value-label for="w" class="peer-checked/w:text-blue-600 ml-1" :value="__('Windows')" />

            <x-radio-input wire:model="form.line_endings" value="l" class="peer/l ml-4" />
            <x-value-label for="l" class="peer-checked/l:text-blue-600 ml-1" :value="__('Linux')" />

            <x-input-error :messages="$errors->get('line_endings')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="char_encoding" :value="__('Convert accented characters to TeX?')" class="mt-4" />
        
            <x-radio-input wire:model="form.char_encoding" value="utf8" class="peer/utf8" /> 
            <x-value-label for="utf8" class="peer-checked/utf8:text-blue-600 ml-1" :value="__('Yes')" />

            <x-radio-input wire:model="form.char_encoding" value="utf8leave" class="peer/utf8leave ml-4" />
            <x-value-label for="l" class="peer-checked/utf8leave:text-blue-600 ml-1" :value="__('No')" />

            <x-input-error :messages="$errors->get('char_encoding')" class="mt-2" />

            <div class="mt-2 dark:text-gray-300">
                If you are using biblatex to process your BibTeX file, you can safely choose "No". 
            </div>
        </div>

        <div>
            <x-input-label for="percent_comment" :value="__('Treat % as starting a comment?')" class="mt-4" />
        
            <x-radio-input wire:model="form.percent_comment" value="1" class="peer/1" /> 
            <x-value-label for="1" class="peer-checked/1:text-blue-600 ml-1" :value="__('Yes')" />

            <x-radio-input wire:model="form.percent_comment" value="0" class="peer/0 ml-4" />
            <x-value-label for="0" class="peer-checked/0:text-blue-600 ml-1" :value="__('No')" />

            <x-input-error :messages="$errors->get('percent_comment')" class="mt-2" />

            <x-option-info class="peer-checked/1:block">
                Every "%" not preceded by "\" and the characters that follow up to the end of the line will be removed before the item is processed.
            </x-option-info>
            <x-option-info class="peer-checked/0:block">
                Every "%" will be treated as a regular character.
            </x-option-info>
        </div>

        <div>
            <x-input-label for="include_source" :value="__('Include each reference as comment above entry in BibTeX file?')" class="mt-4" />
        
            <x-radio-input wire:model="form.include_source" value="1" class="peer/1" /> 
            <x-value-label for="1" class="peer-checked/1:text-blue-600 ml-1" :value="__('Yes')" />

            <x-radio-input wire:model="form.include_source" value="0" class="peer/0 ml-4" />
            <x-value-label for="0" class="peer-checked/0:text-blue-600 ml-1" :value="__('No')" />

            <x-input-error :messages="$errors->get('include_source')" class="mt-2" />

            <x-option-info class="peer-checked/1:block">
                The original reference will be included as a comment above each item in the BibTeX file that the system creates (making it easier for you to check the correctness of the conversion).
            </x-option-info>
            <x-option-info class="peer-checked/0:block">
                The original references will not be included in the BibTeX file.
            </x-option-info>
        </div>

        <div>
            <x-input-label for="report_type" :value="__('Report type')"  class="mt-4"/>
        
            <x-radio-input wire:model="form.report_type" value="standard" class="peer/standard" /> 
            <x-value-label for="standard" class="peer-checked/standard:text-blue-600 ml-1" :value="__('Standard')" />

            <x-radio-input wire:model="form.report_type" value="detailed" class="peer/detailed ml-4" />
            <x-value-label for="detailed" class="peer-checked/detailed:text-blue-600 ml-1" :value="__('Detailed')" />

            <x-input-error :messages="$errors->get('report_type')" class="mt-2" />

            <x-option-info class="peer-checked/standard:block">
                Standard information is provided about the conversion of each item.
            </x-option-info>
            <x-option-info class="peer-checked/detailed:block">
                Detailed information is included about the logic behind the conversion.  This setting is useful only if you want to suggest code changes to improve the conversion. 
            </x-option-info>
        </div>

        <div>
            <x-input-label for="save_settings" :value="__('Save settings as default for future conversions?')" class="mt-4" />

            <x-radio-input wire:model="form.save_settings" value="1" class="peer/1" /> 
            <x-value-label for="1" class="peer-checked/1:text-blue-600 ml-1" :value="__('Yes')" />

            <x-radio-input wire:model="form.save_settings" value="0" class="peer/0 ml-4" />
            <x-value-label for="0" class="peer-checked/0:text-blue-600 ml-1" :value="__('No')" />
        </div>

        <div id="numberProcessed">
            0
        </div>

        <!--
        <div>
            <p>
                Number of items processed: <input type="text" wire:model="numberProcessed" value="{{ $numberProcessed }}" />
            </p>
        </div>
        -->

        <div class="pt-4">
            <x-primary-button class="ml-0">
                {{ __('Submit') }}
            </x-primary-button>
        </div>

    </form>

    @script
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('update-number-processed', (numberProcessed) => {
                document.getElementById("numberProcessed").innerText = numberProcessed;
            });
        });
    </script>
    @endscript

</div>

