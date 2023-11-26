<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Convert from text to BibTeX') }}
        </h2>
    </x-slot>

    <div class="py-0">
        <div class="max-w-7xl mx-auto sm:px-0 lg:px-0 space-y-6">
            <div class="p-4 sm:p-8 pt-0 sm:pt-0 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-2xl">

                    <form method="POST" action="{{ route('file.upload') }}" method="POST" enctype="multipart/form-data" class="mt-0 space-y-0">
                        @csrf

                        <div>
                            <x-input-label for="file" :value="__('File')" />
                            <x-text-input id="file" class="block mt-1 w-full" type="file" name="file" accept="txt"
                                required autofocus />
                            <x-input-error :messages="$errors->get('file')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="incremental" :value="__('Incremental conversion?')" class="mt-4" />
                        
                            <x-radio-input name="incremental" id="incremental" value="1" class="peer/1" :checked="old('incremental', true)" /> 
                            <x-value-label for="1" class="peer-checked/1:text-blue-600 ml-1" :value="__('Yes')" />

                            <x-radio-input name="incremental" id="incremental" value="0" class="peer/0 ml-4" />
                            <x-value-label for="0" class="peer-checked/0:text-blue-600 ml-1" :value="__('No')" />

                            <x-input-error :messages="$errors->get('incremental')" class="mt-2" />

                            <div class="mt-2 hidden peer-checked/1:block">
                                The items in your file will be converted one at a time.  After each item is converted, you will be able to accept the conversion, edit the converted item, or edit the source and re-do the conversion.
                            </div>
                            <div class="mt-2 hidden peer-checked/0:block">
                                All the items in your file will be converted in one pass.
                            </div>
                        </div>

                        <div>
                            <x-input-label for="item_separator" :value="__('Item separator')"  class="mt-4"/>
                        
                            <x-radio-input name="item_separator" id="item_separator" value="line" class="peer/line" :checked="old('item_separator', true)" /> 
                            <x-value-label for="line" class="peer-checked/line:text-blue-600 ml-1" :value="__('Blank line')" />

                            <x-radio-input name="item_separator" id="item_separator" value="cr" class="peer/cr ml-4" />
                            <x-value-label for="cr" class="peer-checked/cr:text-blue-600 ml-1" :value="__('Carriage return')" />

                            <x-input-error :messages="$errors->get('item_separator')" class="mt-2" />

                            <div class="mt-2 hidden peer-checked/line:block">
                                The items  in your file are separated by blank lines.  Carriage returns within items will be treated as spaces.
                            </div>
                            <div class="mt-2 hidden peer-checked/cr:block">
                                Every line in your file is a separate item.
                            </div>
                        </div>

                        <div>
                            <x-input-label for="first_component" :value="__('First component of each item in your source file')" class="mt-4" />
                        
                            <x-radio-input name="first_component" id="first_component" value="authors" class="peer/authors" :checked="old('first_component', true)" /> 
                            <x-value-label for="authors" class="peer-checked/authors:text-blue-600 ml-1" :value="__('Authors')" />

                            <x-radio-input name="first_component" id="first_component" value="year" class="peer/year ml-4" />
                            <x-value-label for="year" class="peer-checked/year:text-blue-600 ml-1" :value="__('Year')" />

                            <x-input-error :messages="$errors->get('first_component')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="label_style" :value="__('Label style')" class="mt-4" />
                        
                            <x-radio-input name="label_style" id="label_style" value="short" class="peer/short" :checked="old('label_style', true)" /> 
                            <x-value-label for="short" class="peer-checked/short:text-blue-600 ml-1" :value="__('Short')" />

                            <x-radio-input name="label_style" id="label_style" value="long" class="peer/long ml-4" />
                            <x-value-label for="long" class="peer-checked/long:text-blue-600 ml-1" :value="__('Long')" />

                            <x-radio-input name="label_style" id="label_style" value="gs" class="peer/gs ml-4" />
                            <x-value-label for="gs" class="peer-checked/gs:text-blue-600 ml-1" :value="__('Google Scholar')" />

                            <x-checkbox-input name="save_settings" id="save_settings" value="1" class="peer ml-4" :checked="old('save_settings')" />
                            <x-value-label for="save_settings" class="peer-checked:text-blue-600 ml-1" :value="__('Override labels in file (if any)')" />
                            
                            <x-input-error :messages="$errors->get('label_style')" class="mt-2" />

                            <div class="mt-2 hidden peer-checked/short:block">
                                Concatenation of lowercased first letter of each author's last name and last two digits of year.  E.g. Arrow and Hahn (1971) &rArr; ah71
                            </div>
                            <div class="mt-2 hidden peer-checked/long:block">
                                Concatenation of authors' last names and year.  E.g. Arrow and Hahn (1971)  &rArr; ArrowHahn1971
                            </div>
                            <div class="mt-2 hidden peer-checked/gs:block">
                                Concatenation of lowercased first author's last name, year of publication, and lowercased significant first word of title, where "a", "an", "the", and "on" are regarded as insignificant. (I don't know exactly which words Google Scholar treats as insignificant. If you know, please tell me.) If the first author's last name contains a space, my algorithm uses the last segment, whereas Google Scholar may use the first segment.  E.g. Arrow and Hahn (1971) &rArr; arrow1971general
                            </div>

                            <div class="mt-2 peer-checked:hidden">
                                For any  item in your file that starts with <code>\bibitem{&lt;label&gt;}</code>, where <code>&lt;label&gt;</code> is a string, that string is used as the label for the item; the label style you have selected is ignored for this item. 
                            </div>
                            <div class="mt-2 hidden peer-checked:block">
                                For any item in your file that starts with <code>\bibitem{&lt;label&gt;}</code>, where <code>&lt;label&gt;</code> is a string, that string is ignored and a label is constructed according to the option you have selected.
                            </div>
                        </div>

                        <div>
                            <x-input-label for="line_endings" :value="__('Line-ending style for output')" class="mt-4" />
                        
                            <x-radio-input name="line_endings" id="line_endings" value="w" class="peer/w" :checked="old('line_endings', true)" /> 
                            <x-value-label for="w" class="peer-checked/w:text-blue-600 ml-1" :value="__('Windows')" />

                            <x-radio-input name="line_endings" id="line_endings" value="l" class="peer/l ml-4" />
                            <x-value-label for="l" class="peer-checked/l:text-blue-600 ml-1" :value="__('Linux')" />

                            <x-input-error :messages="$errors->get('line_endings')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="char_encoding" :value="__('Character encoding')" class="mt-4" />
                        
                            <x-radio-input name="char_encoding" id="char_encoding" value="utf8" class="peer/utf8" :checked="old('char_encoding', true)" /> 
                            <x-value-label for="utf8" class="peer-checked/utf8:text-blue-600 ml-1" :value="__('utf-8 (convert to TeX)')" />

                            <x-radio-input name="char_encoding" id="char_encoding" value="utf8leave" class="peer/utf8leave ml-4" />
                            <x-value-label for="l" class="peer-checked/utf8leave:text-blue-600 ml-1" :value="__('utf-8 (leave as is)')" />

                            <x-radio-input name="char_encoding" id="char_encoding" value="ascii" class="peer/ascii ml-4" />
                            <x-value-label for="l" class="peer-checked/ascii:text-blue-600 ml-1" :value="__('ASCII')" />

                            <x-radio-input name="char_encoding" id="char_encoding" value="windows1252" class="peer/windows1252 ml-4" />
                            <x-value-label for="l" class="peer-checked/windows1252:text-blue-600 ml-1" :value="__('Windows 1252')" />

                            <x-input-error :messages="$errors->get('char_encoding')" class="mt-2" />

                            <div class="mt-2">
                                If you don't know, utf-8 (convert to TeX), the default, is fairly safe. If you are using biblatex to process your BibTeX file, you can use the option "utf8 (leave as is)". If you want to learn all about encodings, read <x-link href="http://kunststube.net/encoding/" target="_blank">this superb article</x-link>. 
                            </div>
                        </div>

                        <div>
                            <x-input-label for="percent_comment" :value="__('Treat % as starting a comment?')" class="mt-4" />
                        
                            <x-radio-input name="percent_comment" id="percent_comment" value="1" class="peer/1" :checked="old('percent_comment', true)" /> 
                            <x-value-label for="1" class="peer-checked/1:text-blue-600 ml-1" :value="__('Yes')" />

                            <x-radio-input name="percent_comment" id="percent_comment" value="0" class="peer/0 ml-4" />
                            <x-value-label for="0" class="peer-checked/0:text-blue-600 ml-1" :value="__('No')" />

                            <x-input-error :messages="$errors->get('percent_comment')" class="mt-2" />

                            <div class="mt-2 hidden peer-checked/1:block">
                                Every "%" not preceded by "\" and the characters that follow up to the end of the line will be removed before the item is processed.
                            </div>
                            <div class="mt-2 hidden peer-checked/0:block">
                                Every "%" will be treated as a regular character.
                            </div>
                        </div>

                        <div>
                            <x-input-label for="include_source" :value="__('Include each reference as comment above entry in BibTeX file?')" class="mt-4" />
                        
                            <x-radio-input name="include_source" id="include_source" value="1" class="peer/1" :checked="old('include_source', true)" /> 
                            <x-value-label for="1" class="peer-checked/1:text-blue-600 ml-1" :value="__('Yes')" />

                            <x-radio-input name="include_source" id="include_source" value="0" class="peer/0 ml-4" />
                            <x-value-label for="0" class="peer-checked/0:text-blue-600 ml-1" :value="__('No')" />

                            <x-input-error :messages="$errors->get('include_source')" class="mt-2" />

                            <div class="mt-2 hidden peer-checked/1:block">
                                The original reference will be included as a comment above each item in the BibTeX file that the system creates (making it easier for you to check the correctness of the conversion).
                            </div>
                            <div class="mt-2 hidden peer-checked/0:block">
                                The original references will not be included in the BibTeX file.
                            </div>
                        </div>

                        <div>
                            <x-input-label for="save_settings" :value="__('Save settings as default for future conversions?')" class="mt-4" />

                            <x-checkbox-input name="save_settings" id="save_settings" value="1" class="ml-0" :checked="old('save_settings', true)" />
                        </div>
    
                        {{-- 
                        {if $citationUserGroups and !$citationUserGroups->wasEmpty()}
                            {assign var="first" value=1}
                            <tr>
                                <td class="label">Verified citation user group</td>
                                <td class="value">
                                    {iterate from=citationUserGroups item=group}
                                    <input type="radio" name="citationUserGroupId" value="{$group->getGroupId()}"{if $first} checked="checked"{assign var="first" value=0}{/if} />{$group->getGroupName()}
                                    {/iterate}
                                </td>
                            </tr>
                        {else}
                            <input type="hidden" name="citationUserGroupId" value="0" />
                        {/if}
                        {if $isSiteAdmin}
                            <tr>
                                <td class="label">Debug?</td>
                                <td class="value"><input type="radio" name="debug" value="1" checked="checked" />Yes
                                    <input type="radio" name="debug" value="0" />No
                                </td>
                            </tr>
                        {else}
                            <input type="hidden" name="debug" value="0" />
                        {/if}
                        --}}

                        <div class="pt-4">
                            <x-primary-button class="ml-0">
                                {{ __('Submit') }}
                            </x-primary-button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

</x-app-layout>
