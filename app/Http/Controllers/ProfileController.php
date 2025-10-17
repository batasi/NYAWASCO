<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $user->load('profile');

        return view('profile.edit', [
            'user' => $user,
            'title' => 'Edit Profile - EventSphere'
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|max:2048',
            'current_password' => 'nullable|required_with:new_password|current_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        // Update password if provided
        if ($request->filled('new_password')) {
            $validated['password'] = Hash::make($validated['new_password']);
        }

        // Remove password fields if not changing password
        if (!$request->filled('current_password')) {
            unset($validated['current_password'], $validated['new_password'], $validated['new_password_confirmation']);
        }

        $user->update($validated);

        // Update or create profile
        $this->updateProfile($request, $user);

        return back()->with('success', 'Profile updated successfully!');
    }

    private function updateProfile(Request $request, $user)
    {
        $profileData = $request->validate([
            'company_name' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'tax_id' => 'nullable|string|max:100',
        ]);

        // Handle social links
        $socialLinks = [];
        $socialPlatforms = ['facebook', 'twitter', 'instagram', 'linkedin', 'youtube'];

        foreach ($socialPlatforms as $platform) {
            if ($request->filled("social_links.{$platform}")) {
                $socialLinks[$platform] = $request->input("social_links.{$platform}");
            }
        }

        $profileData['social_links'] = !empty($socialLinks) ? $socialLinks : null;

        if ($user->profile) {
            $user->profile->update($profileData);
        } else {
            $user->profile()->create($profileData);
        }
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Soft delete user (if using soft deletes)
        // Or permanently delete based on your requirements
        $user->update(['is_active' => false]);

        // Alternative: Permanently delete
        // auth()->logout();
        // $user->delete();

        return redirect()->route('home')->with('success', 'Your account has been deactivated.');
    }

    public function preferences()
    {
        $user = Auth::user();
        $preferences = $user->preferences ?? [
            'email_notifications' => true,
            'sms_notifications' => false,
            'newsletter' => true,
            'event_recommendations' => true,
            'voting_updates' => true,
        ];

        return view('profile.preferences', [
            'user' => $user,
            'preferences' => $preferences,
            'title' => 'Notification Preferences - EventSphere'
        ]);
    }

    public function updatePreferences(Request $request)
    {
        $validated = $request->validate([
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'newsletter' => 'boolean',
            'event_recommendations' => 'boolean',
            'voting_updates' => 'boolean',
        ]);

        $user = auth()->user();
        $user->update([
            'preferences' => array_merge($user->preferences ?? [], $validated)
        ]);

        return back()->with('success', 'Preferences updated successfully!');
    }

    public function security()
    {
        return view('profile.security', [
            'title' => 'Security Settings - EventSphere'
        ]);
    }

    public function updateSecurity(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($validated['new_password'])
        ]);

        return back()->with('success', 'Password updated successfully!');
    }

    public function activity()
    {
        $user = auth()->user();

        $activities = [
            'ticket_purchases' => $user->ticketPurchases()
                ->with('event')
                ->latest()
                ->take(10)
                ->get(),
            'votes' => $user->votes()
                ->with(['contest', 'nominee'])
                ->latest()
                ->take(10)
                ->get(),
        ];

        return view('profile.activity', [
            'user' => $user,
            'activities' => $activities,
            'title' => 'My Activity - EventSphere'
        ]);
    }
}
