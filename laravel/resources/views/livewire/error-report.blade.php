<div>
    {{ '@' }}{{ $output->itemType->name }}{
        <ul class="ml-6">
        @foreach ($output->fields as $field)
            <li>{{ $field->itemField->name }} = {{ '{' }}{{ $field->content }}{{ '}' }}</li>
        @endforeach
        </ul>
        }
    <br/>

    @if ($displayState == 'block')
        <div style="display: none;" id="text1{{ $output->id }}">
    @else
        <div style="display: block;" id="text1{{ $output->id }}">
    @endif
        <a class="text-blue-500 dark:text-blue-400 cursor-pointer" onclick="toggleReportForm({{ $output->id }});">Correct entry and report conversion error</a>
    </div>

    <div style="display:{{ $displayState }};" class="dark:bg-slate-600 bg-slate-300 p-4 mt-4" id="reportForm{{ $output->id }}">
        <form method="POST" wire:submit="submit({{ $outputId }})" id="form{{ $outputId }}">
            @csrf
            <div class="mb-2">
                @php
                    $selected[$itemTypeId] = 1;                
                @endphp
                <x-input-label for="itemTypeId" value="Item type"/>
                <x-select-input id="itemTypeId" name="itemTypeId" class="block mt-1 w-full" :options="$itemTypeOptions" :selected="$selected" wire:model.change="itemTypeId" />
            </div>

            @foreach ($fields as $field)
                @if ($field['name'] != 'kind')
                @php
                    $name = $field['name'];
                    $modelName = 'form.' . $name;
                @endphp
                <div>
                    <x-input-label :for="$name" :value="$name" />
                    <x-text-input :id="$name" class="block mt-1 w-full" type="text" :name="$name" :wire:model="$modelName"/>
                </div>
                @endif
            @endforeach
            <x-input-label for="comment" value="Comment regarding conversion error" />
            <x-textarea-input rows="2" id="comment" class="block mt-1 w-full" type="text" name="comment" value="" wire:model="form.comment"/>

            @if ($status == 'noChange')
                <div class="mt-2">
                    <span class="text-red-500">You have made no changes</span>
                </div>
            @elseif ($status == 'changes')
            <div class="mt-2">
                <span class="text-green-600">Changes saved and report filed</span>
            </div>
            @endif

            <x-primary-button class="ml-0 mt-3">
                {{ __('Save corrected entry and submit conversion error report') }}
            </x-primary-button>

            <x-secondary-button class="ml-0 mt-3">
                <a class="text-blue-500 dark:text-blue-400 cursor-pointer" onclick="toggleReportForm({{ $outputId }});">Cancel/Hide form</a>
            </x-secondary-button>

        </form>                                            
    </div>
</div>


