<nav x-data="{ open: false }"">
    <!-- Primary Navigation Menu -->
    <div class="px-4 sm:px-4 lg:px-4">
        <div class="flex justify-between h-16 border-blue-700 border-b-4 dark:border-blue-100">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center text-blue-700 text-2xl font-bold">
                    <a href="{{ route('dashboard') }}">
                        text2bib
                    </a>
                </div>
            </div>
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Log in</a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="ml-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Register</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</nav>
