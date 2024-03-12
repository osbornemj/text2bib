<x-guest-layout>
    <div>
        <div class="px-4 my-4 mb-4 text-gray-600 dark:text-gray-400">
            {{ __('You have been sent an email message.  (Please check your spam folder if it\'s not in your inbox.  Adding the address noreply@economics.utoronto.ca to your list of contacts will increase the chance messages from this site land in your inbox.)  Click on the link in the message to confirm your email address.') }}
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="px-4 my-4 mb-4 text-green-600 dark:text-green-400">
                {{ __('A new verification message has been sent to the email address you provided during registration.') }}
            </div>
        @endif

        <div class="px-4 my-4 mt-4 items-center justify-between">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf

                <div>
                    <x-primary-button>
                        {{ __('Resend Verification Email') }}
                    </x-primary-button>
                </div>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                    {{ __('Log Out') }}
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
