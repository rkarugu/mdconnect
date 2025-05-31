
@extends('layouts.app')


@section('content')
    <h2 class="text-xl font-bold mb-4">Change Password</h2>

    <form method="POST" action="{{ route('admin.change-password.update') }}">
        @csrf

        <div class="mb-4">
            <label class="block">Current Password</label>
            <input type="password" name="current_password" required class="w-full border rounded px-3 py-2">
        </div>

        <div class="mb-4">
            <label class="block">New Password</label>
            <input type="password" name="new_password" required class="w-full border rounded px-3 py-2">
        </div>

        <div class="mb-4">
            <label class="block">Confirm New Password</label>
            <input type="password" name="new_password_confirmation" required class="w-full border rounded px-3 py-2">
        </div>

        <button class="bg-blue-500 text-white px-4 py-2 rounded">Update Password</button>
    </form>
@endsection
