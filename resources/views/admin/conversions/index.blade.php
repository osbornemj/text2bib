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
                    <x-link href="{{ url('/admin/showConversion/' . $conversion->id) }}">Source file & BibTeX file</x-link>
                    &nbsp;&bull;&nbsp;
                    {{ $conversion->outputs_count }} {{ Str::plural('item', $conversion->outputs_count ) }}:
                    @foreach ($conversion->correctnessCounts() as $key => $value)
                        <span class="@if ($key == -1) bg-red-500 @elseif ($key == 1) bg-emerald-500 @else bg-slate-500 @endif">{{ $value }}</span>
                    @endforeach
                    &nbsp;&bull;&nbsp;
                    <x-link href="{{ url('/admin/convert/' . $conversion->user_file_id) }}">convert (line sep)</x-link>
                    &nbsp;&bull;&nbsp;
                    <x-link href="{{ url('/admin/convert/' . $conversion->user_file_id . '/cr') }}">convert (cr sep)</x-link>
                    <br/>
                    <div class="ml-4">
                        {{ $conversion->user->fullName() }}
                        &nbsp;&bull;&nbsp;
                        {{ $conversion->created_at }}
                    </div>
                </li>
            @endforeach
            </ul>
        </div>
        {{ $conversions->links() }}
    </div>
</x-app-layout>
