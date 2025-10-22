@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 m-0">Users</h1>
        <form method="get" class="d-flex" style="gap:.5rem;">
            <input type="search" name="q" class="form-control" placeholder="Search name/email" value="{{ $q }}">
            <button class="btn btn-outline-secondary">Search</button>
        </form>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
            <tr>
                <th>#</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th style="width:280px">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td><span class="badge {{ $user->role==='admin'?'bg-primary':'bg-secondary' }}">{{ $user->role }}</span></td>
                    <td>
                        @if($user->is_banned)
                            <span class="badge bg-danger">Banned</span>
                            @if($user->ban_reason)<small class="text-muted d-block">Reason: {{ $user->ban_reason }}</small>@endif
                        @else
                            <span class="badge bg-success">Active</span>
                        @endif
                    </td>
                    <td class="d-flex" style="gap:.35rem;">
                        <a class="btn btn-sm btn-outline-info" href="{{ route('admin.users.edit',$user) }}">Edit</a>

                        @if(!$user->is_banned)
                            <form method="post" action="{{ route('admin.users.ban',$user) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="ban_reason" value="">
                                <button class="btn btn-sm btn-outline-warning" onclick="return askReason(this)">Ban</button>
                            </form>
                        @else
                            <form method="post" action="{{ route('admin.users.unban',$user) }}">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-outline-success">Unban</button>
                            </form>
                        @endif

                        <form method="post" action="{{ route('admin.users.destroy',$user) }}" onsubmit="return confirm('Delete this user?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted">No users.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{ $users->links() }}

    <script>
        function askReason(btn){
            const reason = prompt('Ban reason (optional):');
            if (reason === null) return false;
            btn.closest('form').querySelector('input[name="ban_reason"]').value = reason || '';
            return true;
        }
    </script>
@endsection
