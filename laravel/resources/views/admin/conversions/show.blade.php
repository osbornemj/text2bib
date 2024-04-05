<x-app-layout>

    <div class="sm:px-0 pt-4 space-y-6">
        <div class="px-4 pt-0 sm:rounded-lg">
                All conversions: <x-link href="{{ url('admin/conversions?page=' . $page) }}">current page</x-link>
                &nbsp;&bull;&nbsp;
                <x-link href="{{ url('admin/conversions') }}">first page</x-link>
        </div>
    </div>

    <div class="sm:px-0 pt-4 space-y-6">
        <div class="px-4 sm:rounded-lg">
            <div class="ml-0">
                @if ($conversion->user)
                    {{ $conversion->user->fullName() }}
                    &nbsp;&bull;&nbsp;
                @endif
                {{ $conversion->created_at }}
                &nbsp;&bull;&nbsp;
                <x-link href="{{ url('admin/downloadSource/' . $conversion->user_file_id) }}">source file</x-link>
                @if ($conversion->version)
                    &nbsp;&bull;&nbsp;
                    code version {{ $conversion->version }}
                @endif
                <br/>
                @include('index.partials.settings')
                <br/>
                @if ($conversion->examined_at)
                <div class="mt-1">
                    <span class="text-black bg-emerald-500 px-2 text-sm uppercase rounded">Examined {{ $conversion->examined_at }}</span>
                    @if ($conversion->admin_comment)
                        &nbsp;
                        <span class="text-emerald-500">{{ $conversion->admin_comment }}</span>
                        &nbsp;
                    @endif
                    [<x-link href="{{ url('admin/conversionUnexamined/' . $conversion->id) }}">remove</x-link>]
                </div>
                @else
                <form method="POST" action="{{ url('/admin/conversionExamined') }}" class="mt-2 space-y-0">
                    @csrf
                    Comment
                    <input type="hidden" id="conversionId" name="conversionId" value="{{ $conversion->id }}"/>
                    <input type="hidden" id="page" name="page" value="{{ $page }}"/>
                    <x-text-input id="admin_omment" name="admin_comment" class="p-2 w-80"></x-text-input>
                    <x-primary-button class="ml-0">
                        {{ __('Record conversion examined') }}
                    </x-primary-button>
                </form>
                @endif
            </div>

            @foreach ($outputs as $i => $output)
                <div>
                    <livewire:admin-converted-item :output="$output" />
                </div>
            @endforeach
        </div>
    </div>

</x-app-layout>
