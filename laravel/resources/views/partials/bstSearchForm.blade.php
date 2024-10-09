<form method="GET" action="{{ route('bsts.search') }}">
    @csrf

    <div>
        Type:
        @foreach ($types as $type)
            <x-checkbox-input name="{{ $type }}" value="1" class="ml-4" :checked="empty($input) || isset($input[$type])"/> 
            <x-value-label for="type" class="ml-1" :value="$type" />
        @endforeach
    </div>

    <div>
        Fields: standard fields and
        @foreach ($fields as $field)
            <x-checkbox-input id="{{ $field}}" name="{{ $field }}" value="1" class="ml-2" :checked="isset($input[$field])"/> {{ $field}}
        @endforeach
    </div>

    <div>
        <div class="flex">
            <x-small-submit-button class="my-2 py-1">
                {{ __('Find') }}
            </x-small-submit-button>
        </div>
    </div>
</form>
