<form method="POST" action="{{ route('admin.search.conversions') }}">
    @csrf

    <div>
        <x-input-label for="search_string" :value="__('Strings in source')" class="mt-4 mb-1"/>
        <x-text-input id="search_string" name="search_string" value="{{ $searchString ?? '' }}" class="block mt-1 w-full" type="text" autofocus />
    </div>
    <div>
        <x-input-label for="cutoff_date" :value="__('Corrected by user?')" class="mt-1 mb-1"/>
        <x-checkbox-input id="corrected_by_user" name="corrected_by_user" value="1" :checked="$correctedByUser ?? false" class="block mt-1" />
    </div>
    <div>
        <x-input-label for="cutoff_date" :value="__('Created since')" class="mt-1 mb-1"/>
        <div class="flex">
            <x-text-input id="cutoff_date" name="cutoff_date" value="{{ $cutoffDate ?? '' }}" class="block mt-1" type="date" />
            <x-primary-button class="ml-4 mt-1 py-0">
                {{ __('Search all conversions') }}
            </x-primary-button>
        </div>
    </div>
</form>
