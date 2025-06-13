<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(10); // Show 10 users per page
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all(); // Fetch all roles from DB
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'role' => 'required|exists:roles,id',
        ]);

        // Extract first name from full name
        $firstName = explode(' ', $request->name)[0];
        $defaultPassword = bcrypt($firstName . '@medcon');

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $defaultPassword,
            'role_id' => $request->role,
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $roles = Role::all(); // Fetch all roles from DB
        return view('admin.users.edit', compact('user', 'roles')); // Pass both $user and $roles to the view
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20',
        'role' => 'required|exists:roles,id',
        'password' => 'nullable|string|min:6|confirmed', // password confirmation is optional
            
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role_id' => $request->role,
        ];
    
        // Only update password if provided
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }
    
        $user->update($data);
    
        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
