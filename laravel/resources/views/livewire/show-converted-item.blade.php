<div>
    @if ($status == 'changes' && (count($convertedItem['warnings']) || count($convertedItem['notices'])))
        <p>
            <i>Warnings/notices from original conversion:</i>
        </p>
    @endif

    @if (count($convertedItem['warnings']))
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

    <div class="mt-2">
        {{ '@' }}{{ $convertedItem['itemType'] }}{{ '{' }}{{ $convertedItem['label'] }},
            <ul class="ml-6">
                @foreach ($fields as $field)
                    @isset($convertedItem['item']->$field)
                        <li>{{ $field }} = {{ '{' }}{{ $convertedItem['item']->$field }}{{ '}' }},</li>
                    @endisset
                @endforeach
            </ul>
        {{ '}' }}
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

    {{--
        @if ($status == 'changes')
            @if ($priorReportExists)
                <span class="text-green-600">Report updated</span>
            @elseif ($correctionExists)
                <span class="text-green-600">Changes saved and report filed</span>  
            @else
                <span class="text-green-600">Changes saved</span>
            @endif
            <br/>
        @endif
    --}}

    </div>

    @if ($status == 'changes') 
        <button class="mt-2 inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest focus:outline-none transition ease-in-out duration-150 bg-blue-500">Corrected</button>
    @else
        @if ($correctness == 1)
            <x-basic-button wire:click="setCorrectness(0)" class="ml-0 mt-2 bg-emerald-600 dark:bg-emerald-600">
                {{ __('Correct') }}
            </x-basic-button>
        @else
            <x-basic-button wire:click="setCorrectness(1)" class="ml-0 mt-2 bg-slate-600 dark:bg-slate-300">
                {{ __('Correct') }}
            </x-basic-button>
        @endif

        @if ($correctness == -1)
            <x-basic-button wire:click="setCorrectness(0)" class="ml-0 mt-2 bg-red-500 dark:bg-red-400">
                {{ __('Incorrect') }}
            </x-basic-button>
        @else 
            <x-basic-button wire:click="setCorrectness(-1)" class="ml-0 mt-2 bg-slate-600 dark:bg-slate-300">
                {{ __('Incorrect') }}
            </x-basic-button>
        @endif
    @endif

    @if ($displayState == 'block')
        <div style="display: none;" id="text1{{ $outputId }}">
    @else
        <div style="display: block;" id="text1{{ $outputId }}">
    @endif

        @if ($correctionExists)
            @if ($correctionsEnabled)
                <a class="text-blue-500 dark:text-blue-400 cursor-pointer" wire:click="showForm">Edit your correction</a>
            @else
                Your conversion error report can no longer be edited because someone has commented on it.
            @endif
        @else
            {{--
            <a class="text-blue-500 dark:text-blue-400 cursor-pointer" wire:click="showForm">Correct entry and optionally report conversion error</a>
            --}}
        @endif
    </div>

    <div style="display:{{ $displayState }};" class="dark:bg-slate-600 bg-slate-300 p-4 mt-4" id="reportForm{{ $outputId }}">
        <form method="POST" wire:submit="submit()" id="form{{ $outputId }}">
            @csrf
            <div class="mb-2">
                @php
                    $selected[$itemTypeId] = 1;                
                @endphp
                <x-input-label for="itemTypeId" value="Item type"/>
                <x-select-input id="itemTypeId" name="itemTypeId" class="block mt-1 w-full" :options="$itemTypeOptions" :selected="$selected" wire:model.change="itemTypeId" />
            </div>

            @foreach ($fields as $name)
                @php
                    $modelName = 'form.' . $name;
                    $value = $convertedItem['item']->$name ?? null;
                @endphp
                <div>
                    <x-input-label :for="$name" :value="$name" />
                    <x-text-input :id="$name" class="block mt-1 w-full" type="text" :name="$name" :value="$value" :wire:model="$modelName"/>
                </div>
            @endforeach

            @if ($correctionsEnabled)
                <div>
                    <x-checkbox-input id="postReport" class="peer" type="checkbox" value="1" name="postReport" wire:model="form.postReport" />
                    <span class="text-sm font-medium ml-1 text-gray-700 dark:text-gray-300">Report conversion error?</span>
                    <div class="hidden peer-checked:block">
                        <x-input-label for="comment" value="Comment on error (optional)" />
                        <x-textarea-input rows="2" id="comment" class="block mt-1 w-full" name="comment" value="" wire:model="form.comment"/>
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
                <a class="text-blue-500 dark:text-blue-400 cursor-pointer" wire:click="hideForm"> @if(!$status) Cancel @else Hide form @endif </a>
            </x-secondary-button>

        </form>                                            
    </div>
</div>
</div>



