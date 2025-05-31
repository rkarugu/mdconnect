<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class SystemSetupController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        $users = User::all();
        return view('admin.system-setup', compact('roles', 'users'));
    }
}
