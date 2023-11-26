<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Users
        </h2>
    </x-slot>

    <div class="sm:px-0 lg:px-0 space-y-6">
        <div class="px-4 sm:px-4 sm:rounded-lg">
            <ul>
            @foreach ($users as $user)
                <li>{{  $user->fullName(true) }} ({{ $user->email }}    )</li>
            @endforeach
            </ul>
            {{ $users->links() }}
        </div>
    </div>
</x-app-layout>
