
@extends('layouts.app')

@section('title', 'System Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">System Settings</h3>
                </div>
                <div class="card-body">
                    <h4>Roles Management</h4>
                    <table class="table table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Role Name</th>
                                <th>Permissions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $role)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $role->name }}</td>
                                    <td>
                                        @foreach($role->permissions as $permission)
                                            <span class="badge badge-success">{{ $permission->name }}</span>
                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <h4 class="mt-4">Permissions List</h4>
                    <table class="table table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Permission Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($permissions as $permission)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $permission->name }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <h4 class="mt-4">User Roles</h4>
                    <table class="table table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>User Name</th>
                                <th>Email</th>
                                <th>Roles</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @foreach($user->roles as $role)
                                            <span class="badge badge-info">{{ $role->name }}</span>
                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div> 
            </div> 
        </div>
    </div>
</div>
@endsection
