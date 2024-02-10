<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Users
        </h2>
    </x-slot>

    <div class="sm:px-0 lg:px-0 space-y-6">
        <div class="px-4 sm:px-4 sm:rounded-lg">
            <!-- Grid wrapper -->
            @foreach ($users as $user)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-4">
                    <div>
                        {{ $user->fullName(true) }}
                        @if ($user->is_admin)
                            (admin)
                        @endif
                    </div>
                    <div>
                        {{ $user->email }}
                    </div>
                    <div>
                        {{ $user->email_verified_at }}
                    </div>
                    <div>
                        {{ $user->wants_messages }}
                    </div>
                </div>
            @endforeach


            {{ $users->links() }}
        </div>
    </div>
</x-app-layout>
