<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use Illuminate\Auth\Events\Registered;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;



    public function create(array $input): User
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'in:attendee,organizer,vendor'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ];

        Validator::make($input, $rules)->validate();

        $userData = [
            'name' => $input['name'],
            'email' => $input['email'],
            'phone' => $input['phone'] ?? null,
            'role' => $input['role'],
            'password' => Hash::make($input['password']),
            'occupation' => $input['occupation'] ?? null,
            'institution' => $input['institution'] ?? null,
            'membership_number' => $input['membership_number'] ?? null,
            'attendee_type' => $input['attendee_type'] ?? null,
            'company_name' => $input['company_name'] ?? null,
            'business_name' => $input['business_name'] ?? null,
            'website' => $input['website'] ?? null,
            'about' => $input['about'] ?? null,
            'address' => $input['address'] ?? null,
            'city' => $input['city'] ?? null,
            'state' => $input['state'] ?? null,
            'country' => $input['country'] ?? null,
            'postal_code' => $input['postal_code'] ?? null,
            'tax_id' => $input['tax_id'] ?? null,
            'business_registration_number' => $input['business_registration_number'] ?? null,
            'contact_number' => $input['contact_number'] ?? null,
            'services_offered' => $input['services_offered'] ?? null,
        ];

        // Remove null values
        $userData = array_filter($userData, function ($value) {
            return $value !== null;
        });

        // Create the user
        $user = User::create($userData);
        $user->assignRole($input['role']);

        // Trigger email verification
        event(new Registered($user));

        return $user;
    }
}
