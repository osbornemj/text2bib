<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Users
        </h2>
    </x-slot>

    <div class="px-4 mb-2">
        {{ $users->total() }} found
        @if ($searchString)
            &nbsp;&bull;&nbsp;
            <x-link href="{{ url('admin/users') }}">All users</x-link>
        @endif
    </div>

    <div class="m-4 -mt-2">
        <form method="POST" action="{{ route('admin.search.users') }}">
            @csrf
    
            <div>
                <x-input-label for="search_string" :value="__('String in name')" class="mt-4 mb-1"/>
                <div class="flex">
                    <x-text-input id="search_string" name="search_string" class="block mt-1 max-w-xl w-full" type="text" value="{{ $searchString }}" autofocus />
                    <x-primary-button class="ml-4 py-0">
                        {{ __('Search') }}
                    </x-primary-button>
                </div>
            </div>
        </form>
    </div>

    <div class="sm:px-0 lg:px-0 space-y-6">
        <div class="px-4 sm:px-4 sm:rounded-lg">
            <!-- Grid wrapper -->
            <div class="grid grid-cols-1 md:grid-cols-6 lg:grid-cols-12 xl:grid-cols-12 2xl:grid-cols-12">
                <div class="hidden md:col-span-4 md:block lg:col-span-3 lg:border-b-2 mb-2">
                    @if ($sortBy == 'name')
                        <div class="bg-slate-100 dark:bg-slate-700">
                    @endif
                    <x-link href="{{ url('admin/users/name') }}">Name</x-link>
                    @if ($sortBy == 'name')
                        </div>
                    @endif
                </div>
                <div class="hidden md:col-span-5 md:block lg:col-span-4 lg:border-b-2 mb-2">
                    Email
                </div>
                <div class="hidden md:col-span-1 md:block lg:col-span-2 md:border-b-2 mb-2">
                    @if ($sortBy == 'registered')
                        <div class="bg-slate-100 dark:bg-slate-700">
                    @endif
                    <x-link href="{{ url('admin/users/registered') }}">Registered</x-link>
                    @if ($sortBy == 'registered')
                        </div>
                    @endif
                </div>
                <div class="hidden md:col-span-1 md:block lg:col-span-2 md:border-b-2 mb-2">
                    @if ($sortBy == 'lastLogin')
                        <div class="bg-slate-100 dark:bg-slate-700">
                    @endif
                    <x-link href="{{ url('admin/users/lastLogin') }}">Last login</x-link>
                    @if ($sortBy == 'lastLogin')
                        </div>
                    @endif
                </div>
                <div class="hidden md:col-span-1 md:block lg:col-span-1 md:border-b-2 mb-2">
                    @if ($sortBy == 'conversionCount')
                        <div class="bg-slate-100 dark:bg-slate-700">
                    @endif
                    <x-link href="{{ url('admin/users/conversionCount') }}"># conv</x-link>
                    @if ($sortBy == 'conversionCount')
                        </div>
                    @endif
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
                        @if ($user->conversions_count)
                            <x-link href="{{ url('admin/conversions/' . $user->id) }}">
                        @endif
                        {{ $user->conversions_count }}
                        <span class="lg:hidden">
                            conversions
                        </span>
                        @if ($user->conversions_count)
                            </x-link>
                        @endif
                    </div>
                @endforeach
            </div>

            {{ $users->links() }}
        </div>
    </div>
</x-app-layout>
