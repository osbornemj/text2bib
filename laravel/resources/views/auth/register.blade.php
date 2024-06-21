<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div style="display: none;">
            <input id="empty" type="text" size="32" maxlength="32" class="form-control" name="empty" >
        </div>

        <div class="mt-4">
            <p>
                Please enter your real name, not a made-up name.
            </p>
        </div>
        
        <!-- First name -->
        <div>
            <x-input-label for="first_name" :value="__('First name')" class="mt-6" />
            <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name')" required autofocus autocomplete="given-name" />
            <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
        </div>

        <!-- Middle name -->
        <div class="mt-4">
            <x-input-label for="middle_name" :value="__('Middle name')" />
            <x-text-input id="middle_name" class="block mt-1 w-full" type="text" name="middle_name" :value="old('middle_name')" autocomplete="additional-name" />
            <x-input-error :messages="$errors->get('middle_name')" class="mt-2" />
        </div>

        <!-- Last name -->
        <div class="mt-4">
            <x-input-label for="last_name" :value="__('Last name')" />
            <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name')" required autocomplete="family-name" />
            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email address')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="source" :value="__('How did you discover this website?')" class="mt-4 mb-1"/>
    
            @foreach ($sourceOptions as $key => $option)
                <x-radio-input name="source" value="{{ $key }}" checked="{{ ($key == 'other' && old('source') == 'other') || ($key == 'otherSite' && old('source') == 'otherSite') }}" @endif class="peer/{{ $key }}" /> 
                <x-value-label for="{{ $key }}" class="peer-checked/{{ $key }}:text-blue-600 ml-1" :value="$option" />
                <br/>
            @endforeach
    
            <x-input-error :messages="$errors->get('source')" class="mt-2" />
                
            <div class="hidden peer-checked/otherSite:block">
                <x-text-input name="source_other_site" type="url" class="w-full" />
            </div>

            <x-input-error :messages="$errors->get('source_other_site')" class="mt-2" />
            
            <div class="hidden peer-checked/other:block">
                <x-text-input name="source_other" class="w-full" />
            </div>

            <x-input-error :messages="$errors->get('source_other')" class="mt-2" />
        </div>

        <div class="flex items-center mt-4">
            <x-primary-button>
                {{ __('Register') }}
            </x-primary-button>

            <x-link href="{{ route('login') }}" class="ml-6">
                {{ __('Already registered?') }}
            </x-link>
        </div>
    </form>
</x-guest-layout>
