<x-app-layout>

    <div class="pt-4 flex justify-center">
        <div class="sm:px-4 lg:px-4 space-y-6">
            <div class="sm:p-0 pt-0 sm:pt-0">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="sm:pt-2 sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="sm:pt-2 sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
