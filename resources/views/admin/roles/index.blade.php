@extends('layouts.app')

@section('title', 'Roles & Permissions')

@section('content')
    <div class="container-fluid">
        <h2>Roles & Permissions</h2>
        
        <!-- Link to the roles index page -->
        <a href="{{ route('roles.index') }}" class="btn btn-secondary mb-3">Roles</a>
        
        <!-- Create Role Link (opens the create role form) -->
        <a href="{{ route('roles.create') }}" class="btn btn-primary mb-3">Create Role</a>

        <!-- Role List -->
        <table class="table mt-3">
            <thead>
                <tr>
                    <th>Role</th>
                    <th>Permissions</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $role)
                    <tr>
                        <td>{{ $role->name }}</td>
                        <td>
                            @foreach($role->permissions as $permission)
                                <span class="badge badge-info">{{ $permission->name }}</span>
                            @endforeach
                        </td>
                        <td>
                            <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
