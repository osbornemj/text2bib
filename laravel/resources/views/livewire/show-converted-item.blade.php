<div>
    @if ($status == 'changes' && (count($convertedItem['warnings']) || count($convertedItem['notices'])))
        <p>
            <i>Warnings/notices from original conversion:</i>
        </p>
    @endif

    @if (count($convertedItem['warnings']) && $language != 'my')
    <ul>
        @foreach ($convertedItem['warnings'] as $warning)
            <li>
                <span class="text-red-600">{{ $warning }}</span>
            </li>
        @endforeach
    </ul>
    @endif
    
    @if (count($convertedItem['notices']))
    <ul>
        @foreach ($convertedItem['notices'] as $notice)
            <li>
                <span class="text-orange-600">{{ $notice }}</span>
            </li>
        @endforeach
    </ul>
    @endif

    @if (count($convertedItem['infos']))
    <ul>
        @foreach ($convertedItem['infos'] as $info)
            <li>
                <span class="text-green-600">{{ $info }}</span>
            </li>
        @endforeach
    </ul>
    @endif

    <div class="mt-2">

        @if (! isset($convertedItem['crossref_item_type']) || $convertedItem['orig_item_type'] == $convertedItem['crossref_item_type'])

        <form id="item{{ $outputId }}">
            <code>{{ '@' }}{{ $convertedItem['itemType'] }}</code>{{ '{' }}{{ $convertedItem['label'] }},
                <ul class="ml-6">
                    @foreach ($fields as $field)
                        @if (isset($convertedItem['item']->$field))
                            <li>
                                <span class="text-gray-800 dark:text-gray-200"><code>{{ $field }}</code> = {{ '{' }}{{ $convertedItem['item']->$field }}{{ '}' }}</span>,
                            </li>
                        @endif

                        @if (isset($convertedItem['orig_item']->$field) && isset($convertedItem['crossref_item']->$field))
                            @if ($convertedItem['orig_item']->$field != $convertedItem['crossref_item']->$field)
                                <li class="ml-4">
                                    <x-radio-input name="{{ $field }}" wire:click="setFieldSource('{{ $field }}', 'conversion')" class="peer/t2b" checked /> 
                                    &nbsp; 
                                    <span class="text-blue-700 dark:text-blue-300">{{ $convertedItem['orig_item']->$field }}</span>
                                </li>
                                <li class="ml-4">
                                    <x-radio-input name="{{ $field }}" wire:click="setFieldSource('{{ $field }}', 'crossref')" class="peer/cr" /> 
                                    &nbsp; 
                                    <span class="text-orange-800 dark:text-orange-300">{{ $convertedItem['crossref_item']->$field }}</span>
                                </li>
                            @endif
                        @elseif (isset($convertedItem['crossref_item']->$field))
                            <div>
                                <li class="ml-4">
                                    <x-checkbox-input value="cr" wire:click="addCrossrefField('{{ $field }}')" class="peer/cr" /> 
                                    &nbsp; 
                                    <span class="text-orange-800 dark:text-orange-300">{{ $field }} = {{ '{' }}{{ $convertedItem['crossref_item']->$field }}{{ '}' }}</span>            
                                </li>
                            </div>
                        @endif
                    @endforeach
                </ul>
            {{ '}' }}
        </form>

        @else

            <p class="my-2">
                text2bib's analysis of your source assigns this item the type <code>{{ $convertedItem['orig_item_type']}}</code> but Crossref assigns it the type <code>{{ $convertedItem['crossref_item_type']}}</code>:
            </p>

            <form id="choice{{ $outputId }}">
                <code>{{ '@' }}{{ $convertedItem['itemType'] }}</code>{{ '{' }}{{ $convertedItem['label'] }},
                <ul class="ml-6">
                    @foreach ($fields as $field)
                        @if (isset($convertedItem['item']->$field))
                            <li>
                                <code>{{ $field }}</code> = <span class="text-gray-800 dark:text-gray-200">{{ '{' }}{{ $convertedItem['item']->$field }}{{ '}' }}</span>,
                            </li>
                        @endif
                    @endforeach
                </ul>
                {{ '}' }}

                <div class="mt-2">
                    <x-radio-input wire:model="source" wire:click="setItemSource('conversion')" value="conversion" class="peer/t2b" />
                    &nbsp;
                    <code>{{ '@' }}{{ $convertedItem['orig_item_type'] }}</code>{{ '{' }}{{ $convertedItem['label'] }},
                    <ul class="ml-10">
                        @foreach ($origFields as $field)
                            @if (isset($convertedItem['orig_item']->$field))
                                <li>
                                    <code>{{ $field }}</code> = <span class="text-blue-700 dark:text-blue-300">{{ '{' }}{{ $convertedItem['orig_item']->$field }}{{ '}' }}</span>,
                                </li>
                            @endif
                        @endforeach
                    </ul>
                    {{ '}' }}
                </div>

                <div class="mt-2">
                    <x-radio-input wire:model="source" wire:click="setItemSource('crossref')" value="crossref" class="peer/cr" />
                    &nbsp;
                    <code>{{ '@' }}{{ $convertedItem['crossref_item_type'] }}</code>{{ '{' }}{{ $convertedItem['label'] }},
                    <ul class="ml-10">
                        @foreach ($crossrefFields as $field)
                            @if (isset($convertedItem['crossref_item']->$field))
                                <li>
                                    <code>{{ $field }}</code> = <span class="text-orange-800 dark:text-orange-300">{{ '{' }}{{ $convertedItem['crossref_item']->$field }}{{ '}' }}</span>,
                                </li>
                            @endif
                        @endforeach
                    </ul>
                    {{ '}' }}
                </div>
            </form>

        @endif
    </div>

    <div class="mt-2">
        Check in
        <x-link href="https://scholar.google.com/scholar?q={{ $convertedItem['scholarTitle'] }}&num=100&btnG=Search+Scholar&as_sdt=1.&as_sdtp=on&as_sdtf=&as_sdts=5&hl=en" target="_blank">Google Scholar</x-link>
        &nbsp;&bull;&nbsp;
        <x-link href="https://www.jstor.org/action/doAdvancedSearch?q0={{ $convertedItem['scholarTitle'] }}&f0=ti&c1=AND&q1=&f1=ti&wc=on&Search=Search&sd=&ed=&la=&jo=')" target="_blank">JSTOR</x-link>
        [new tab/window]
    </div>

    @if ($displayState == 'block')
        <div style="display: none;" id="text1{{ $outputId }}">
    @else
        <div style="display: block;" id="text1{{ $outputId }}">
    @endif
        </div>

    @if ($correctness == 2) 
        <button class="mt-2 inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest focus:outline-none transition ease-in-out duration-150 bg-blue-500">Corrected</button>
    @else
        @if ($correctness == 1)
            <x-basic-button wire:click="setCorrectness(0)" class="ml-0 mt-2 bg-emerald-600 dark:bg-emerald-600">
                {{ __('Mark as correct') }}
            </x-basic-button>
        @else
            <x-basic-button wire:click="setCorrectness(1)" class="ml-0 mt-2 bg-slate-400 dark:bg-slate-300">
                {{ __('Mark as correct') }}
            </x-basic-button>
        @endif

        @if ($correctness == -1)
            <x-basic-button wire:click="setCorrectness(0)" class="ml-0 mt-2 bg-red-500 dark:bg-red-400">
                {{ __('Edit') }}
            </x-basic-button>
        @else 
            <x-basic-button wire:click="setCorrectness(-1)" class="ml-0 mt-2 bg-slate-400 dark:bg-slate-300">
                {{ __('Edit') }}
            </x-basic-button>
        @endif
    @endif

    @if ($displayState == 'block')
        <div style="display: none;" id="text1{{ $outputId }}">
    @else
        <div style="display: block;" id="text1{{ $outputId }}">
    @endif

        @if ($correctness == 2)
            <a class="text-blue-500 dark:text-blue-400 cursor-pointer" wire:click="showForm">Edit your correction</a>
        @endif

        {{--
        @if ($correctionExists)
            @if ($correctionsEnabled)
                <a class="text-blue-500 dark:text-blue-400 cursor-pointer" wire:click="showForm">Edit your correction</a>
            @else
                Your conversion error report can no longer be edited because someone has commented on it.
            @endif
        @else
            <a class="text-blue-500 dark:text-blue-400 cursor-pointer" wire:click="showForm">Correct entry and optionally report conversion error</a>
        @endif
        --}}
    </div>

    {{-- Following line stopped working around 2024.9.23.  Replaced with next two lines. --}}
    {{--<div style="display:{{ $displayState }};" class="dark:bg-slate-600 bg-slate-300 p-4 mt-4" id="reportForm{{ $outputId }}">--}}

    @if ($displayState == "block")
        <div class="dark:bg-slate-600 bg-slate-300 p-4 mt-4" id="reportForm{{ $outputId }}">
            <form method="POST" wire:submit="submit()" onsubmit="myScrollTo({{ $outputId }});" id="form{{ $outputId }}">
                @csrf
                <div class="mb-2">
                    @php
                        $selected[$itemTypeId] = 1;                
                    @endphp
                    <x-input-label for="itemTypeId" value="Item type"/>
                    <x-select-input id="itemTypeId" name="itemTypeId" class="block mt-1 w-full" :options="$itemTypeOptions" :selected="$selected" wire:model.change="itemTypeId" />
                </div>

                @foreach ($fields as $field)
                    <div>
                        <x-input-label :for="$field" :value="$field" />
                        <x-text-input :id="$field" class="block mt-1 w-full" type="text" :name="$field" value="{{ $convertedItem['item']->$field ?? '' }}" :wire:model="$field"/>
                    </div>
                @endforeach

                @if ($correctionsEnabled)
                    <div>
                        <x-checkbox-input id="postReport" class="peer" type="checkbox" value="1" name="postReport" wire:model="postReport" />
                        <span class="text-sm font-medium ml-1 text-gray-700 dark:text-gray-300">Report conversion error?</span>
                        <div class="hidden peer-checked:block">
                            <x-input-label for="comment" value="Comment on error (optional)" />
                            <x-textarea-input rows="2" id="comment" class="block mt-1 w-full" name="comment" value="" wire:model="comment"/>
                        </div>
                    </div>
                @endif

                @if ($status == 'noChange')
                    <div class="mt-2">
                        <span class="text-red-500">You have made no changes</span>
                    </div>
                @endif

                <x-primary-button class="ml-0 mt-3">
                    {{ __('Submit correction') }}
                </x-primary-button>

                <x-secondary-button class="ml-0 mt-3">
                    <a class="text-blue-500 dark:text-blue-400 cursor-pointer" wire:click="hideForm" onclick="myScrollTo({{ $outputId }});"> @if(!$status) Cancel @else Hide form @endif </a>
                </x-secondary-button>

            </form>                                            
        </div>
    @endif
</div>




