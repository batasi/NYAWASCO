@php
use Illuminate\Support\Facades\Route;
@endphp
<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}" id="registrationForm">
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
                <x-label for="phone" value="{{ __('Phone Number') }}" />
                <x-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone')" autocomplete="tel" />
            </div>

            <!-- Role Selection -->
            <div class="mt-4">
                <x-label for="role" value="{{ __('Register As') }}" />
                <select id="role" name="role" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                    <option value="">Select Your Role</option>
                    <option value="attendee" {{ old('role') == 'attendee' ? 'selected' : '' }}>Attendee</option>
                    <option value="organizer" {{ old('role') == 'organizer' ? 'selected' : '' }}>Event Organizer</option>
                    <option value="vendor" {{ old('role') == 'vendor' ? 'selected' : '' }}>Vendor</option>
                </select>
            </div>

            <!-- Attendee Specific Fields -->
            <div id="attendeeFields" class="hidden mt-4 space-y-4">
                <div>
                    <x-label for="occupation" value="{{ __('Occupation') }}" />
                    <x-input id="occupation" class="block mt-1 w-full" type="text" name="occupation" :value="old('occupation')" />
                </div>
                <div>
                    <x-label for="institution" value="{{ __('Institution/Company') }}" />
                    <x-input id="institution" class="block mt-1 w-full" type="text" name="institution" :value="old('institution')" />
                </div>
                <div>
                    <x-label for="membership_number" value="{{ __('Membership Number (Optional)') }}" />
                    <x-input id="membership_number" class="block mt-1 w-full" type="text" name="membership_number" :value="old('membership_number')" />
                </div>
                <div>
                    <x-label for="attendee_type" value="{{ __('Attendee Type') }}" />
                    <select id="attendee_type" name="attendee_type" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">Select Type</option>
                        <option value="voter" {{ old('attendee_type') == 'voter' ? 'selected' : '' }}>Voter</option>
                        <option value="event-goer" {{ old('attendee_type') == 'event-goer' ? 'selected' : '' }}>Event Goer</option>
                    </select>
                </div>
            </div>

            <!-- Organizer Specific Fields -->
            <div id="organizerFields" class="hidden mt-4 space-y-4">
                <div>
                    <x-label for="company_name" value="{{ __('Company Name') }}" />
                    <x-input id="company_name" class="block mt-1 w-full" type="text" name="company_name" :value="old('company_name')" />
                </div>
                <div>
                    <x-label for="website" value="{{ __('Website') }}" />
                    <x-input id="website" class="block mt-1 w-full" type="url" name="website" :value="old('website')" />
                </div>
                <div>
                    <x-label for="about" value="{{ __('About Your Organization') }}" />
                    <textarea id="about" name="about" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('about') }}</textarea>
                </div>
                <div>
                    <x-label for="address" value="{{ __('Business Address') }}" />
                    <x-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address')" />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-label for="city" value="{{ __('City') }}" />
                        <x-input id="city" class="block mt-1 w-full" type="text" name="city" :value="old('city')" />
                    </div>
                    <div>
                        <x-label for="state" value="{{ __('State/Region') }}" />
                        <x-input id="state" class="block mt-1 w-full" type="text" name="state" :value="old('state')" />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-label for="country" value="{{ __('Country') }}" />
                        <x-input id="country" class="block mt-1 w-full" type="text" name="country" :value="old('country')" />
                    </div>
                    <div>
                        <x-label for="postal_code" value="{{ __('Postal Code') }}" />
                        <x-input id="postal_code" class="block mt-1 w-full" type="text" name="postal_code" :value="old('postal_code')" />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-label for="tax_id" value="{{ __('Tax ID') }}" />
                        <x-input id="tax_id" class="block mt-1 w-full" type="text" name="tax_id" :value="old('tax_id')" />
                    </div>
                    <div>
                        <x-label for="business_registration_number" value="{{ __('Business Registration Number') }}" />
                        <x-input id="business_registration_number" class="block mt-1 w-full" type="text" name="business_registration_number" :value="old('business_registration_number')" />
                    </div>
                </div>
            </div>

            <!-- Vendor Specific Fields - SIMPLIFIED -->
            <div id="vendorFields" class="hidden mt-4 space-y-4">
                <div>
                    <x-label for="business_name" value="{{ __('Business Name') }}" />
                    <x-input id="business_name" class="block mt-1 w-full" type="text" name="business_name" :value="old('business_name')" />
                </div>
                <div>
                    <x-label for="contact_number" value="{{ __('Business Contact Number') }}" />
                    <x-input id="contact_number" class="block mt-1 w-full" type="tel" name="contact_number" :value="old('contact_number')" />
                </div>
                <div>
                    <x-label for="services_offered" value="{{ __('Services Offered') }}" />
                    <textarea id="services_offered" name="services_offered" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Describe the services you offer...">{{ old('services_offered') }}</textarea>
                </div>
                <div>
                    <x-label for="address" value="{{ __('Business Address') }}" />
                    <x-input id="vendor_address" class="block mt-1 w-full" type="text" name="address" :value="old('address')" />
                </div>
                <div>
                    <x-label for="website" value="{{ __('Website') }}" />
                    <x-input id="vendor_website" class="block mt-1 w-full" type="url" name="website" :value="old('website')" />
                </div>
                <div>
                    <x-label for="tax_id" value="{{ __('Tax ID') }}" />
                    <x-input id="vendor_tax_id" class="block mt-1 w-full" type="text" name="tax_id" :value="old('tax_id')" />
                </div>
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
                            'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 hover:text-gray-900">'.__('Terms of Service').'</a>',
                            'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 hover:text-gray-900">'.__('Privacy Policy').'</a>',
                            ]) !!}
                        </div>
                    </div>
                </x-label>
            </div>
            @endif

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-button class="ms-4" type="submit">
                    {{ __('Register') }}
                </x-button>
            </div>
        </form>

        <div class="my-4 flex items-center">
            <hr class="flex-grow border-gray-300">
            <span class="px-2 text-gray-500 text-sm">or</span>
            <hr class="flex-grow border-gray-300">
        </div>

        <!-- Continue with Google Button -->
        <div class="mt-6">
            <a href="{{ route('google.login') }}"
                class="inline-flex items-center justify-center w-full px-4 py-2 text-white bg-red-600 rounded-lg hover:bg-red-700 transition">
                <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google" class="w-5 h-5 mr-2">
                {{ __('Continue with Google') }}
            </a>
        </div>

    </x-authentication-card>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('role');
            const attendeeFields = document.getElementById('attendeeFields');
            const organizerFields = document.getElementById('organizerFields');
            const vendorFields = document.getElementById('vendorFields');

            function showRoleFields(role) {
                // Hide all fields first
                attendeeFields.classList.add('hidden');
                organizerFields.classList.add('hidden');
                vendorFields.classList.add('hidden');

                // Show fields based on selected role
                switch (role) {
                    case 'attendee':
                        attendeeFields.classList.remove('hidden');
                        break;
                    case 'organizer':
                        organizerFields.classList.remove('hidden');
                        break;
                    case 'vendor':
                        vendorFields.classList.remove('hidden');
                        break;
                }
            }

            // Show fields based on initial value (for form validation errors)
            if (roleSelect.value) {
                showRoleFields(roleSelect.value);
            }

            // Add event listener for role change
            roleSelect.addEventListener('change', function() {
                showRoleFields(this.value);
            });

            // Remove the client-side validation that was blocking form submission
            // Let the server-side validation in CreateNewUser handle it
            document.getElementById('registrationForm').addEventListener('submit', function(e) {
                // Let the form submit normally - server validation will handle required fields
                console.log('Form submitting with role:', roleSelect.value);
            });
        });
    </script>
</x-guest-layout>