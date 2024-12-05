{{-- <x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div>
                <x-label for="name" value="{{ __('Name') }}" />
                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            </div>

            <div class="mt-4">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required />

                            <div class="ms-2">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Terms of Service').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-button class="ms-4">
                    {{ __('Register') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout> --}}


<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
            @csrf

            <div>
                <x-label for="name" value="{{ __('Name') }}" />
                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            </div>

            <div class="mt-4">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="phone" value="{{ __('Phone') }}" />
                <x-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" required autocomplete="tel" />
            </div>

            <div class="mt-4">
                <x-label for="account_type" value="{{ __('Account Type') }}" />
                <select id="account_type" name="account_type" class="block mt-1 w-full" required>
                    <option value="">{{ __('Select Account Type') }}</option>
                    <option value="serviteur_de_dieu">{{ __('Serviteur de Dieu') }}</option>
                    <option value="fidele">{{ __('Fidèle') }}</option>
                    <option value="chantre">{{ __('Chantre') }}</option>
                </select>
            </div>

            {{-- Champs pour "Serviteur de Dieu" --}}
            <div class="mt-4" id="serviteur_fields" style="display:none;">
                <x-label for="id_card_recto" value="{{ __('ID Card Recto') }}" />
                <x-input id="id_card_recto" class="block mt-1 w-full" type="file" name="id_card_recto" accept="image/*" />

                <x-label for="id_card_verso" value="{{ __('ID Card Verso') }}" />
                <x-input id="id_card_verso" class="block mt-1 w-full" type="file" name="id_card_verso" accept="image/*" />



                <div class="mt-4">
                    <x-label for="is_main" value="{{ __('Is Main Servant?') }}" />
                    <input type="hidden" name="is_main" value="0">
                    <x-checkbox id="is_main" name="is_main" value="1" />
                </div>

            </div>

            {{-- Champs pour "Chantre" --}}
            {{-- <div class="mt-4" id="chantre_fields" style="display:none;">
                <x-label for="manager" value="{{ __('Manager') }}" />
                <x-input id="manager" class="block mt-1 w-full" type="text" name="manager" :value="old('manager')" />

                <x-label for="description" value="{{ __('Description') }}" />
                <x-input id="description" class="block mt-1 w-full" type="text" name="description" :value="old('description')" />
            </div> --}}

            <div class="mt-4">
                <x-label for="adresse" value="{{ __('Address') }}" />
                <x-input id="adresse" class="block mt-1 w-full" type="text" name="adresse" :value="old('adresse')" required />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required />

                            <div class="ms-2">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Terms of Service').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-button class="ms-4">
                    {{ __('Register') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const accountTypeSelect = document.getElementById('account_type');
        const serviteurFields = document.getElementById('serviteur_fields');
        const chantreFields = document.getElementById('chantre_fields');

        accountTypeSelect.addEventListener('change', function () {
            const selectedValue = accountTypeSelect.value;
            serviteurFields.style.display = selectedValue === 'serviteur_de_dieu' ? 'block' : 'none';
            chantreFields.style.display = selectedValue === 'chantre' ? 'block' : 'none';
        });

        // Init display based on current selection
        const selectedValue = accountTypeSelect.value;
        serviteurFields.style.display = selectedValue === 'serviteur_de_dieu' ? 'block' : 'none';
        chantreFields.style.display = selectedValue === 'chantre' ? 'block' : 'none';
    });
</script>



<script>
    document.addEventListener('DOMContentLoaded', function () {
        const accountTypeSelect = document.getElementById('account_type');
        const serviteurFields = document.getElementById('serviteur_fields');

        accountTypeSelect.addEventListener('change', function () {
            const selectedValue = accountTypeSelect.value;

            // Afficher les champs si "Serviteur de Dieu" est sélectionné
            if (selectedValue === 'serviteur_de_dieu') {
                serviteurFields.style.display = 'block';
            } else {
                serviteurFields.style.display = 'none';
            }
        });

        // Au chargement de la page, afficher les champs si "Serviteur de Dieu" est déjà sélectionné
        if (accountTypeSelect.value === 'serviteur_de_dieu') {
            serviteurFields.style.display = 'block';
        }
    });
</script>
