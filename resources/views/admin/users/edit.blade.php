@extends('layouts.admin')

@section('content')
    <h1 class="h4 mb-3">Edit User #{{ $user->id }}</h1>

    @if($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif

    <form method="post" action="{{ route('admin.users.update', $user) }}" class="card card-body">
        @csrf @method('PUT')

        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" value="{{ old('name',$user->name) }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" value="{{ old('email',$user->email) }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Role</label>
            <select name="role" class="form-select">
                <option value="user"  @selected(old('role',$user->role)==='user')>user</option>
                <option value="admin" @selected(old('role',$user->role)==='admin')>admin</option>
            </select>
        </div>

        <div class="mb-3 form-check">
            <input class="form-check-input" type="checkbox" name="is_banned" id="is_banned" value="1" @checked(old('is_banned',$user->is_banned))>
            <label class="form-check-label" for="is_banned">Banned</label>
        </div>

        <div class="mb-3">
            <label class="form-label">Ban reason (optional)</label>
            <input type="text" name="ban_reason" value="{{ old('ban_reason',$user->ban_reason) }}" class="form-control">
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-primary">Save</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
@endsection
