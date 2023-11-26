<div>
    Edit any incorrect fields and then click the button.
    <form method="POST" wire:submit="submitErrorReport({{ $output->id }})" action="{{ route('errorReport.submit', ['outputId' => $output->id]) }}">
        @csrf
        <div class="mt-4 mb-2">
            @php
                $selected[$itemTypeId] = 1;                
            @endphp
            <x-input-label for="itemTypeId" value="Item type"/>
            <x-select-input id="itemTypeId" name="itemTypeId" class="block mt-1 w-full" :options="$itemTypeOptions" :selected="$selected" wire:model="itemTypeId" />
        </div>

        @foreach ($fields as $field)
            @if (!in_array($field->itemField->name, ['kind']))
            @php
                $name = $field->itemField->name;
                $value = $field->content;    
            @endphp
            <div>
                <x-input-label :for="$name" :value="$name" />
                <x-text-input :id="$name" class="blockmt-1 w-full" type="text" :name="$name" :value="$value" :wire:model="$name"/>
            </div>
            @endif
        @endforeach
        <x-primary-button class="ml-0 mt-3">
            {{ __('Correct entry and submit conversion error report') }}
        </x-primary-button>
    </form>                                            

</div>
