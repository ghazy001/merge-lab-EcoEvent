@extends('layouts.admin')
@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Tasks</h1>
            <a href="{{ route('admin.tasks.create') }}" class="btn btn-primary">New Task</a>
        </div>

        @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

        <form class="mb-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Filter by project</label>
                    <select name="project_id" class="form-select" onchange="this.form.submit()">
                        <option value="">All</option>
                        @foreach($projects as $id => $title)
                            <option value="{{ $id }}" @selected(request('project_id')==$id)>{{ $title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>

        <div class="table-responsive card shadow-sm">
            <table class="table table-hover mb-0">
                <thead><tr>
                    <th>Title</th><th>Project</th><th>Status</th><th>Due</th><th>Order</th><th class="text-end">Actions</th>
                </tr></thead>
                <tbody>
                @forelse($tasks as $t)
                    <tr>
                        <td>{{ $t->title }}</td>
                        <td>{{ $t->project?->title }}</td>
                        <td><span class="badge bg-secondary">{{ $t->status }}</span></td>
                        <td>{{ optional($t->due_date)->format('d/m/Y') }}</td>
                        <td>{{ $t->order }}</td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.tasks.edit',$t) }}">Edit</a>
                            <form class="d-inline" method="POST" action="{{ route('admin.tasks.destroy',$t) }}">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">No tasks.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">{{ $tasks->links() }}</div>
    </div>
@endsection
