@extends('layouts.app')

@section('title', 'Edit Role')

@section('content')
    <div class="container-fluid">
        <h2>Edit Role: {{ $role->name }}</h2>

        <form action="{{ route('roles.update', $role->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Role Name</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $role->name) }}" required>
            </div>

            <div class="form-group">
                <label for="permissions">Permissions</label>
                <select name="permissions[]" id="permissions" class="form-control" multiple>
                    @foreach($permissions as $permission)
                        <option value="{{ $permission->id }}" 
                            {{ in_array($permission->id, $role->permissions->pluck('id')->toArray()) ? 'selected' : '' }}>
                            {{ $permission->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-success">Update Role</button>
        </form>
    </div>
@endsection
