<div>
    <li>
        <span class="">{{ $field }} = {{ '{' }}{{ $convertedItem['item']->$field }}{{ '}' }}</span>,
    </li>
    <li>
        <x-radio-input value="conversion" wire:model="fieldSource" wire:click="setFieldSource()" class="peer/t2b" /> 
        &nbsp; 
        <span class="text-blue-800 dark:text-blue-300">{{ $convertedItem['orig_item']->$field }}</span>
    </li>
    <li>
        <x-radio-input value="crossref" wire:model="fieldSource" wire:click="setFieldSource()" class="peer/cr" /> 
        &nbsp; 
        <span class="text-orange-800 dark:text-orange-300">{{ $convertedItem['crossref_item'][$field] }}</span>
    </li>
</div>
