<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Item types and fields
        </h2>
    </x-slot>

    <div class="sm:px-0 lg:px-0 space-y-0">
        <div class="px-4 sm:px-4 sm:rounded-lg">
            <h3 class="font-semibold text-xl">Add field to type</h3>
            <form method="POST" action="{{ route('itemTypeField.add') }}">
                @csrf

                <div>
                    <x-input-label for="itemTypeName" :value="__('Type')" />
                    <x-select-input :options="$itemTypeNames" id="itemType_id" class="block mt-1" name="itemType_id" :value="old('itemType_id')" />
                    <x-input-error :messages="$errors->get('itemType_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="itemFieldName" :value="__('Field')" />
                    <x-select-input :options="$itemFieldNames" id="itemField_id" class="block mt-1" name="itemField_id" :value="old('itemField_id')" />
                    <x-input-error :messages="$errors->get('itemField_id')" class="mt-2" />
                </div>

                <div class="flex items-center mt-4">
                    <x-primary-button class="ml-0">
                        {{ __('Add') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

    <div class="sm:px-0 lg:px-0 space-y-6">
        <div class="p-4 sm:p-4 sm:rounded-lg">
            <h3 class="font-semibold text-xl">Item types</h3>
            <x-link href="/admin/itemTypes/create">Add item type</x-link>
            <ul>
                @foreach ($itemTypes as $itemType)
                    <li>
                        {{ $itemType->name }}
                        <ul class="ml-4">
                            @foreach ($itemType->fields as $itemField)
                                <li>
                                    {{ $itemField }} [<x-link href="{{ url('/admin/itemTypeField/remove/' . $itemField . '/' . $itemType->id) }}">remove</x-link>]
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="sm:px-0 lg:px-0 space-y-6">
        <div class="p-4 sm:p-4 sm:rounded-lg">
            <h3 class="font-semibold text-xl">Item fields</h3>
            <x-link href="/admin/itemFields/create">Add item field</x-link>
            <ul>
                @foreach ($itemFields as $itemField)
                    <li>
                        {{ $itemField->name }}
                        <ul class="ml-4">
                            @foreach ($itemField->itemTypes() as $itemType)
                                <li>
                                    {{ $itemType->name }}
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

</x-app-layout>
