<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Constructor to ensure only authorized users can access role management
     */
    public function __construct()
    {
        $this->middleware('role:super-admin'); // Only super-admins can manage roles
    }

    /**
     * Display a listing of the roles.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $roles = Role::all();
    return view('admin.roles.index', compact('roles'));
    
        $roles = Role::with('permissions')->get(); // Fetch roles with permissions
        $permissions = Permission::all(); // Fetch all permissions

        // Return the view with roles and permissions
        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name|max:255',
            'permissions' => 'array|nullable',
        ]);

        // Create a new role
        $role = Role::create(['name' => $request->name]);

        // Attach selected permissions to the role
        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        $permissions = Permission::all(); // Fetch all permissions for editing

        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id . '|max:255',
            'permissions' => 'array|nullable',
        ]);

        // Update the role name
        $role->update(['name' => $request->name]);

        // Sync the selected permissions with the role
        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role)
    {
        // Detach permissions from the role
        $role->permissions()->detach();

        // Delete the role
        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }
}
