@extends('layouts.app')

@section('title', 'System Setup')

@section('content_header')
    <h1>System Setup</h1>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <!-- Role Management Section -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Role Management</h3>
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-primary float-right">Manage Roles</a>
                    </div>
                    <div class="card-body">
                        <ul>
                            @foreach($roles as $role)
                                <li>{{ $role->name }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <!-- User Management Section -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">User Management</h3>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-primary float-right">Manage Users</a>
                    </div>
                    <div class="card-body">
                        <ul>
                            @foreach($users as $user)
                                <li>{{ $user->name }} ({{ $user->email }})</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
