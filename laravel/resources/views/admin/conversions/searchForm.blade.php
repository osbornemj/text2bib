<form method="POST" action="{{ route('admin.search.conversions.post') }}">
    @csrf

    <div>
        <x-input-label for="search_string" :value="__('Strings in source')" class="mt-4 mb-1"/>
        <x-text-input id="search_string" name="search_string" value="{{ $searchString ?? '' }}" class="block mt-1 w-full" type="text" autofocus />
    </div>
    <div class="inline-flex">
        <x-input-label for="correctness" :value="__('User rating')" class="mt-1 mb-1 mr-2"/>
        <x-select-input :options="$userRatings" id="correctness" class="block mt-1" name="correctness" :selected="$selectedCorrectness" />
        <x-input-error :messages="$errors->get('correctness')" class="mt-2" />
    </div>
    <div class="inline-flex">
        <x-input-label for="admin_correctness" :value="__('Admin rating')" class="mt-1 mb-1 mx-2"/>
        <x-select-input :options="$adminRatings" id="admin_correctness" class="block mt-1" name="admin_correctness" :selected="$selectedAdminCorrectness" />
        <x-input-error :messages="$errors->get('admin_correctness')" class="mt-2" />
    </div>
    <div class="inline-flex">
        <x-input-label for="cutoff_date" :value="__('Created since')" class="mt-1 mb-1 mx-2"/>
        <x-text-input id="cutoff_date" name="cutoff_date" value="{{ $cutoffDate ?? '' }}" class="block mt-1" type="date" />
    </div>
    <div class="inline-flex">
        <x-input-label for="exclude_crossref" :value="__('Exclude crossref?')" class="mt-1 mb-1 mx-2"/>
        <x-checkbox-input id="exclude_crossref" name="exclude_crossref" value="1" :checked="$exclude_crossref ?? false" class="block mt-1" />
    </div>
    <div>
        <div class="flex">
            <x-primary-button class="mt-1 py-0">
                {{ __('Search all conversions') }}
            </x-primary-button>
        </div>
    </div>
</form>
