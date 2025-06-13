<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function index()
    {
        $user = Auth::user();
        return view('admin.profile', compact('user'));
    }
    
    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'profile_picture' => ['nullable', 'image', 'max:2048'],
        ]);
        
        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if it exists
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            
            // Store new profile picture
            $path = $request->file('profile_picture')->store('profile-pictures', 'public');
            $user->profile_picture = $path;
        }
        
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? $user->phone;
        $user->save();
        
        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully.');
    }
    
    /**
     * Show the form for changing password.
     */
    public function showChangePasswordForm()
    {
        return view('admin.change-password');
    }
    
    /**
     * Update the user's password.
     */
    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!Hash::check($value, Auth::user()->password)) {
                    $fail('The current password is incorrect.');
                }
            }],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        
        $user = Auth::user();
        $user->password = Hash::make($validated['password']);
        $user->save();
        
        return redirect()->route('admin.profile')->with('success', 'Password changed successfully.');
    }
}
