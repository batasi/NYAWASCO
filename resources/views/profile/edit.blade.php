@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="container mx-auto px-4 py-8">

    <h2 class="text-2xl font-semibold mb-6 text-gray-800">My Profile</h2>

    {{-- Update Profile Information --}}
    <div class="bg-white shadow rounded-lg p-6 mb-8">
        <h3 class="text-lg font-semibold mb-4 text-gray-700">Profile Information</h3>

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-600">Name</label>
                    <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-400">
                </div>

                <div>
                    <label class="block text-sm text-gray-600">Email</label>
                    <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-400">
                </div>

                <div>
                    <label class="block text-sm text-gray-600">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone) }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-400">
                </div>

                <div>
                    <label class="block text-sm text-gray-600">Bio</label>
                    <textarea name="bio" class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-400">{{ old('bio', auth()->user()->bio) }}</textarea>
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    {{-- Update Password --}}
    <div class="bg-white shadow rounded-lg p-6 mb-8">
        <h3 class="text-lg font-semibold mb-4 text-gray-700">Change Password</h3>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-600">Current Password</label>
                    <input type="password" name="current_password" class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-400">
                </div>

                <div>
                    <label class="block text-sm text-gray-600">New Password</label>
                    <input type="password" name="password" class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-400">
                </div>

                <div>
                    <label class="block text-sm text-gray-600">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-400">
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Update Password
                </button>
            </div>
        </form>
    </div>

    {{-- Delete Account --}}
    <div class="bg-white shadow rounded-lg p-6 border border-red-200">
        <h3 class="text-lg font-semibold mb-4 text-red-600">Delete Account</h3>
        <p class="text-gray-600 mb-4">
            Once your account is deleted, all of its resources and data will be permanently deleted. Please confirm your action.
        </p>

        <form method="POST" action="{{ route('profile.destroy') }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
                onclick="return confirm('Are you sure you want to delete your account? This action cannot be undone.')">
                Delete Account
            </button>
        </form>
    </div>
</div>
@endsection
