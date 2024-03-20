<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Conversions
        </h2>
    </x-slot>

    <div class="sm:px-0 lg:px-0 space-y-6">
        <div class="px-4 sm:px-4 pt-0 sm:pt-0 sm:rounded-lg">
            <ul>
            @foreach ($conversions as $conversion)
                <li>
                    <x-link href="{{ url('/admin/showConversion/' . $conversion->id) }}">Conversion</x-link>
                    &nbsp;&bull;&nbsp;
                    {{ $conversion->outputs_count }} {{ Str::plural('item', $conversion->outputs_count ) }}
                    &nbsp;&bull;&nbsp;
                    user
                    @foreach ($conversion->correctnessCounts() as $key => $value)
                        <span class="@if ($key == -1) bg-red-500 @elseif ($key == 1) bg-emerald-500 @else bg-slate-500 @endif text-xs px-1">{{ $value }}</span>
                    @endforeach
                    &nbsp;&bull;&nbsp;
                    admin
                    @foreach ($conversion->adminCorrectnessCounts() as $key => $value)
                        <span class="@if ($key == -1) bg-red-500 @elseif ($key == 1) bg-emerald-500 @else bg-slate-500 @endif text-xs px-1">{{ $value }}</span>
                    @endforeach
                    &nbsp;&bull;&nbsp;
                    <x-link href="{{ url('/admin/convert/' . $conversion->user_file_id) }}">convert (line sep)</x-link>
                    &nbsp;&bull;&nbsp;
                    <x-link href="{{ url('/admin/convert/' . $conversion->user_file_id . '/cr') }}">convert (cr sep)</x-link>
                    <form method="post" action="{{ url('/admin/conversion/' . $conversion->id) }}" class="inline-flex">
                        @csrf
                        @method('delete')
                        <x-danger-button class="ml-2 pb-1 pt-1 pl-1 pr-1 text-xs" onclick="return confirm('Are you sure you want to delete this conversion?');">
                            {{ __('Delete') }}
                        </x-danger-button>
                    </form>
                    <br/>
                    <div class="ml-4">
                        @if ($conversion->user)
                            {{ $conversion->user->fullName() }}
                            &nbsp;&bull;&nbsp;
                        @endif
                        {{ $conversion->created_at }}
                    </div>
                </li>
            @endforeach
            </ul>
        </div>
        {{ $conversions->links() }}
    </div>
</x-app-layout>
