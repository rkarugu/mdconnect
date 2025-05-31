<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;

class SystemSettingsController extends Controller
{
    /**
     * Display the system settings page.
     */
    public function index()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        $users = User::with('roles')->get();

        return view('admin.settings.index', compact('roles', 'permissions', 'users'));
    }
}
