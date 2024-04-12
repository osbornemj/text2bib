<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Users
        </h2>
    </x-slot>

    <div class="px-4 mb-2">
        {{ $users->total() }} found
    </div>

    <div class="sm:px-0 lg:px-0 space-y-6">
        <div class="px-4 sm:px-4 sm:rounded-lg">
            <!-- Grid wrapper -->
            <div class="grid grid-cols-1 md:grid-cols-6 lg:grid-cols-12 xl:grid-cols-12 2xl:grid-cols-12">
                <div class="hidden md:col-span-4 md:block lg:col-span-3 lg:border-b-2">
                    Name
                </div>
                <div class="hidden md:col-span-5 md:block lg:col-span-4 lg:border-b-2">
                    Email
                </div>
                <div class="hidden md:col-span-1 md:block lg:col-span-2 md:border-b-2">
                    Registered
                </div>
                <div class="hidden md:col-span-1 md:block lg:col-span-2 md:border-b-2">
                    Last login
                </div>
                <div class="hidden md:col-span-1 md:block lg:col-span-1 md:border-b-2">
                    # conv
                </div>
                @foreach ($users as $user)
                    <div class="md:col-span-4 lg:col-span-3">
                        {{ $user->fullName(true) }}
                        @if ($user->is_admin)
                            (admin)
                        @endif
                    </div>
                    <div class="md:col-span-5 lg:col-span-4">
                        {{ $user->email }}
                    </div>
                    <div class="md:col-span-1 lg:col-span-2">
                        @if ($user->email_verified_at)
                            {{ $user->email_verified_at ? $user->email_verified_at->toDateString() : '' }}
                        @else
                            <form method="post" action="{{ url('/admin/users/' . $user->id) }}" class="inline-flex">
                                @csrf
                                @method('delete')
                                <x-danger-button class="pb-0 pt-0 pl-1 pr-1 text-xs" onclick="return confirm('Are you sure you want to delete this user?');">
                                    {{ __('Delete') }}
                                </x-danger-button>
                            </form>
                        @endif
                    </div>
                    <div class="md:col-span-1 lg:col-span-2">
                        {{ $user->date_last_login ? $user->date_last_login->toDateString() : '' }}
                    </div>
                    <div class="mb-4 md:col-span-1 lg:mb-0 lg:col-span-1 lg:text-right">
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
