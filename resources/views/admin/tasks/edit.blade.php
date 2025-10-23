@extends('layouts.admin')
@section('content')
    <div class="container py-4">
        <h1>Edit Task: {{ $task->title }}</h1>

        @if($errors->any())
            <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif

        <form method="POST" action="{{ route('admin.tasks.update',$task) }}" class="card p-3 shadow-sm">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Project *</label>
                <select name="project_id" class="form-select" required>
                    @foreach($projects as $id => $title)
                        <option value="{{ $id }}" @selected(old('project_id',$task->project_id)==$id)>{{ $title }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Title *</label>
                <input name="title" class="form-control" value="{{ old('title',$task->title) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="4">{{ old('description',$task->description) }}</textarea>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Status *</label>
                    <select name="status" class="form-select" required>
                        @foreach(['todo','doing','done'] as $s)
                            <option value="{{ $s }}" @selected(old('status',$task->status)===$s)>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Due date</label>
                    <input type="date" name="due_date" class="form-control" value="{{ old('due_date', optional($task->due_date)->format('Y-m-d')) }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Order</label>
                    <input type="number" name="order" min="0" max="65535" class="form-control" value="{{ old('order',$task->order) }}">
                </div>
            </div>

            <button class="btn btn-primary">Update</button>
            <a href="{{ route('admin.tasks.index') }}" class="btn btn-link">Back</a>
        </form>
    </div>
@endsection
