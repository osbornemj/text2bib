<div>
    {{ '@' }}{{ $convertedItem['itemType'] }}{{ '{' }}{{ $convertedItem['label'] }},
        <ul class="ml-6">
            @foreach ($fields as $field)
                @isset($convertedItem['item']->$field)
                    <li>{{ $field }} = {{ '{' }}{{ $convertedItem['item']->$field }}{{ '}' }},</li>
                @endisset
            @endforeach
        </ul>
        {{ '}' }}
    <br/>

    @if ($displayState == 'block')
        <div style="display: none;" id="text1{{ $outputId }}">
    @else
        <div style="display: block;" id="text1{{ $outputId }}">
    @endif
    @if ($status == 'changes')
        @if ($priorReportExists)
            <span class="text-green-600">Report updated</span>
        @elseif ($errorReportExists)
            <span class="text-green-600">Changes saved and report filed</span>
        @else
            <span class="text-green-600">Changes saved</span>
        @endif
        <br/>
    @endif
    @if ($errorReportExists)
        @if ($correctionsEnabled)
            <a class="text-blue-500 dark:text-blue-400 cursor-pointer" wire:click="showForm">Edit your error report</a>
        @else
            Your conversion error report can no longer be edited because someone else has commented on it.
        @endif
    @else
        <a class="text-blue-500 dark:text-blue-400 cursor-pointer" wire:click="showForm">Correct entry and optionally report conversion error</a>
    @endif
    </div>

    <div style="display:{{ $displayState }};" class="dark:bg-slate-600 bg-slate-300 p-4 mt-4" id="reportForm{{ $outputId }}">
        <form method="POST" wire:submit="submit" id="form{{ $outputId }}">
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
                @endphp
                <div>
                    <x-input-label :for="$name" :value="$name" />
                    <x-text-input :id="$name" class="block mt-1 w-full" type="text" :name="$name" :wire:model="$modelName"/>
                </div>
            @endforeach

            @if ($correctionsEnabled)
                <div>
                    <x-checkbox-input id="postReport" class="peer" type="checkbox" value="1" name="postReport" wire:model="form.postReport" />
                    <span class="text-sm font-medium ml-1 text-gray-700 dark:text-gray-300">Report conversion error?</span>

                    <div class="hidden peer-checked:block">
                        <x-input-label for="reportTitle" value="Title of report (short description of error, max 60 characters)" />
                        <x-text-input id="reportTitle" class="block mt-1 w-full" type="text" maxlength="60" name="reportTitle" value="form.reportTitle" wire:model="form.reportTitle"/>
                        <div role="alert" class="mt-4 mb-4">
                            @error('form.reportTitle') <span class="bg-red-500 text-white font-bold rounded px-2 py-1">{{ $message }}</span> @enderror 
                        </div>

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

            @php
                $buttonText = $errorReportExists ? 'Submit correction' : 'Submit correction';
            @endphp

            <x-primary-button class="ml-0 mt-3">
                {{ __($buttonText) }}
            </x-primary-button>

            <x-secondary-button class="ml-0 mt-3">
                <a class="text-blue-500 dark:text-blue-400 cursor-pointer" wire:click="hideForm"> @if(!$status) Cancel @else Hide form @endif </a>
            </x-secondary-button>

        </form>                                            
    </div>
</div>
</div>



