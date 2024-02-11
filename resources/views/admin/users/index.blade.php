<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Users
        </h2>
    </x-slot>

    <div class="sm:px-0 lg:px-0 space-y-6">
        <div class="px-4 sm:px-4 sm:rounded-lg">
            <!-- Grid wrapper -->
            <div class="grid grid-cols-1 md:grid-cols-6 lg:grid-cols-12 xl:grid-cols-12 2xl:grid-cols-12">
                <div class="hidden md:col-span-3 md:block lg:col-span-2 lg:border-b-2">
                    Name
                </div>
                <div class="hidden md:col-span-3 md:block lg:col-span-3 lg:border-b-2">
                    Email
                </div>
                <div class="hidden md:col-span-2 md:block lg:col-span-3 md:border-b-2">
                    Registered
                </div>
                <div class="hidden md:col-span-2 md:block lg:col-span-3 md:border-b-2">
                    Last login
                </div>
                <div class="hidden md:col-span-2 md:block lg:col-span-1 md:border-b-2">
                    # conv
                </div>
                @foreach ($users as $user)
                    <div class="md:col-span-3 lg:col-span-2">
                        {{ $user->fullName(true) }}
                        @if ($user->is_admin)
                            (admin)
                        @endif
                    </div>
                    <div class="md:col-span-3 lg:col-span-3">
                        {{ $user->email }}
                    </div>
                    <div class="md:col-span-2 lg:col-span-3">
                        {{ $user->email_verified_at }}
                    </div>
                    <div class="md:col-span-2 lg:col-span-3">
                        {{ $user->date_last_login }}
                    </div>
                    <div class="mb-4 md:col-span-2 lg:mb-0 lg:col-span-1 lg:text-right">
                        {{ $user->conversions_count }}
                        <span class="lg:hidden">
                            conversions
                        </span>
                    </div>
                @endforeach
            </div>

            {{ $users->links() }}
        </div>
    </div>
</x-app-layout>
