<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Add information about BibTeX style file (bst file)
        </h2>
    </x-slot>

    <div class="sm:px-0 lg:px-0 space-y-6">
        <div class="px-4 sm:px-4 pt-0 sm:pt-0 sm:rounded-lg">
            <form method="POST" action="{{ route('bsts.store') }}">
                @csrf

                <div>
                    <x-input-label for="name" :value="__('Name')" />
                    <x-text-input id="name" class="block mt-1" name="name" :value="old('name')" required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="available" :value="__('File available for checking?')" class="mt-4 mb-1" />
                
                    <x-radio-input name="available" value="1" class="peer/availableTrue" checked/> 
                    <x-value-label for="available" class="peer-checked/available:text-blue-600 ml-1" :value="__('Yes')" />
            
                    <x-radio-input name="available" value="0" class="peer/availableFalse ml-4" />
                    <x-value-label for="available" class="peer-checked/available:text-blue-600 ml-1" :value="__('No')" />
                </div>
            
                <div>
                    <x-input-label for="natbib" :value="__('Requires natbib style file?')" class="mt-4 mb-1" />
                
                    <x-radio-input name="natbib" value="1" class="peer/natbibTrue" /> 
                    <x-value-label for="natbib" class="peer-checked/natbib:text-blue-600 ml-1" :value="__('Yes')" />
            
                    <x-radio-input name="natbib" value="0" class="peer/natbibFalse ml-4" checked />
                    <x-value-label for="natbib" class="peer-checked/natbib:text-blue-600 ml-1" :value="__('No')" />
                </div>
            
                <div>
                    <x-input-label for="doi" :value="__('Supports doi field?')" class="mt-4 mb-1" />
                
                    <x-radio-input name="doi" value="1" class="peer/doiTrue" /> 
                    <x-value-label for="doi" class="peer-checked/doi:text-blue-600 ml-1" :value="__('Yes')" />
            
                    <x-radio-input name="doi" value="0" class="peer/doiFalse ml-4" checked  />
                    <x-value-label for="doi" class="peer-checked/doi:text-blue-600 ml-1" :value="__('No')" />
                </div>
            
                <div>
                    <x-input-label for="url" :value="__('Supports url field?')" class="mt-4 mb-1" />
                
                    <x-radio-input name="url" value="1" class="peer/urlTrue" /> 
                    <x-value-label for="url" class="peer-checked/url:text-blue-600 ml-1" :value="__('Yes')" />
            
                    <x-radio-input name="url" value="0" class="peer/urlFalse ml-4"  checked/>
                    <x-value-label for="url" class="peer-checked/url:text-blue-600 ml-1" :value="__('No')" />
                </div>
            
                <div>
                    <x-input-label for="urldate" :value="__('Supports urldate field?')" class="mt-4 mb-1" />
                
                    <x-radio-input name="urldate" value="1" class="peer/urldateTrue" /> 
                    <x-value-label for="urldate" class="peer-checked/urldate:text-blue-600 ml-1" :value="__('Yes')" />
            
                    <x-radio-input name="urldate" value="0" class="peer/urldateFalse ml-4" checked />
                    <x-value-label for="urldate" class="peer-checked/urldate:text-blue-600 ml-1"  :value="__('No')" />
                </div>
            
                <div>
                    <x-input-label for="doi_escape_underscore" :value="__('Requires underscores in dois to be escaped?')" class="mt-4 mb-1" />
                
                    <x-radio-input name="doi_escape_underscore" value="1" class="peer/doi_escape_underscoreTrue"  checked /> 
                    <x-value-label for="doi_escape_underscore" class="peer-checked/doi_escape_underscore:text-blue-600 ml-1" :value="__('Yes')" />
            
                    <x-radio-input name="doi_escape_underscore" value="0" class="peer/doi_escape_underscoreFalse ml-4" />
                    <x-value-label for="doi_escape_underscore" class="peer-checked/doi_escape_underscore:text-blue-600 ml-1" :value="__('No')" />
                </div>
            
                <div>
                    <x-input-label for="proc_address_conf_location" :value="__('address field for inproceedings is location of conference?')" class="mt-4 mb-1" />
                
                    <x-radio-input name="proc_address_conf_location" value="1" class="peer/proc_address_conf_locationTrue" /> 
                    <x-value-label for="proc_address_conf_location" class="peer-checked/proc_address_conf_location:text-blue-600 ml-1" :value="__('Yes')" />
            
                    <x-radio-input name="proc_address_conf_location" value="0" class="peer/proc_address_conf_locationFalse ml-4" checked />
                    <x-value-label for="proc_address_conf_location" class="peer-checked/proc_address_conf_location:text-blue-600 ml-1" :value="__('No')" />
                </div>
            
                <div>
                    <x-input-label for="translator" :value="__('Supports translator field?')" class="mt-4 mb-1" />
                
                    <x-radio-input name="translator" value="1" class="peer/translatorTrue" /> 
                    <x-value-label for="translator" class="peer-checked/translator:text-blue-600 ml-1" :value="__('Yes')" />
            
                    <x-radio-input name="translator" value="0" class="peer/translatorFalse ml-4" checked />
                    <x-value-label for="translator" class="peer-checked/translator:text-blue-600 ml-1" :value="__('No')" />
                </div>
            
                <div>
                    <x-input-label for="online" :value="__('Supports @online item type?')" class="mt-4 mb-1" />
                
                    <x-radio-input name="online" value="1" class="peer/onlineTrue" /> 
                    <x-value-label for="online" class="peer-checked/online:text-blue-600 ml-1" :value="__('Yes')" />
            
                    <x-radio-input name="online" value="0" class="peer/onlineFalse ml-4" checked />
                    <x-value-label for="online" class="peer-checked/online:text-blue-600 ml-1" :value="__('No')" />
                </div>
            
                <x-input-label for="comment" :value="__('Note')" />
                    <x-textarea-input rows="2" id="note" class="block mt-1 w-full" name="note" value="" />
                </div>
    
                <div class="flex items-center mt-4">
                    <x-primary-button class="ml-0">
                        {{ __('Save') }}
                    </x-primary-button>
                </div>

                <div class="flex items-center mt-4">
                    <x-link href="{{ url('/admin/bsts') }}">All BibTeX style files</x-link>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>
