<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Your conversions') }}
        </h2>
    </x-slot>

    <div class="sm:px-0 lg:px-0 space-y-6">
        <div class="px-4 sm:px-4 pt-0 sm:pt-0 sm:rounded-lg">
            <div class="mb-2">
                Times are <x-link href="https://en.wikipedia.org/wiki/Coordinated_Universal_Time" target="_blank">UTC</x-link>
            </div>
            <ul>
            @foreach ($conversions as $conversion)
                <li>
                    <x-link href="{{ url('/showConversion/' . $conversion->id) }}">{{ $conversion->created_at }}</x-link>
                    ({{ $conversion->outputs_count }} {{ Str::plural('item', $conversion->outputs_count ) }})
                    @foreach ($conversion->correctnessCounts() as $key => $value)
                        <span class="@if ($key == -1) bg-red-500 @elseif ($key == 1) bg-emerald-500 @else bg-slate-500 @endif text-xs px-1">{{ $value }}</span>
                    @endforeach
                </li>
            @endforeach
            </ul>
        </div>
        {{ $conversions->links() }}
    </div>
</x-app-layout>
