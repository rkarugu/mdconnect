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
        // Define role names for categorization
        $systemRoles = ['Super Admin', 'Admin'];
        $facilityAdminRole = 'Facility Admin';
        $medicalWorkerRoles = ['Medical Worker', 'Doctor', 'Nurse']; // Expanded roles

        // Fetch users for each category
        $systemUsers = User::whereHas('role', function ($query) use ($systemRoles) {
            $query->whereIn('name', $systemRoles);
        })->paginate(10, ['*'], 'system_users_page');

        $facilityAdmins = User::whereHas('role', function ($query) use ($facilityAdminRole) {
            $query->where('name', $facilityAdminRole);
        })->paginate(10, ['*'], 'facility_admins_page');

        $medicalWorkers = User::whereHas('role', function ($query) use ($medicalWorkerRoles) {
            $query->whereIn('name', $medicalWorkerRoles);
        })->paginate(10, ['*'], 'medical_workers_page');

        // Combine all categorized roles
        $categorizedRoles = array_merge($systemRoles, [$facilityAdminRole], $medicalWorkerRoles);

        // Fetch users that are not in the categorized roles or have no role
        $otherUsers = User::where(function ($query) use ($categorizedRoles) {
            $query->whereHas('role', function ($subQuery) use ($categorizedRoles) {
                $subQuery->whereNotIn('name', $categorizedRoles);
            })->orWhereDoesntHave('role');
        })->paginate(10, ['*'], 'other_users_page');

        return view('admin.users.index', compact('systemUsers', 'facilityAdmins', 'medicalWorkers', 'otherUsers'));
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
        $roles = Role::all();
        $specialties = \App\Models\MedicalSpecialty::where('is_active', true)->get();
        return view('admin.users.edit', compact('user', 'roles', 'specialties'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20',
            'role_id' => 'required|exists:roles,id',
            'password' => 'nullable|string|min:6|confirmed',
            'medical_specialty_id' => 'nullable|exists:medical_specialties,id',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role_id' => $request->role_id,
            'medical_specialty_id' => $request->medical_specialty_id,
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
