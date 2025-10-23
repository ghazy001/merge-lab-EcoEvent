@extends('layouts.admin')
@section('content')
    <div class="container py-4">
        <h1>Edit: {{ $project->title }}</h1>

        @if($errors->any())
            <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif

        <form method="POST" action="{{ route('admin.projects.update',$project) }}" class="card p-3 shadow-sm">
            @csrf @method('PUT')

            <div class="mb-3">
                <label class="form-label">Title *</label>
                <input name="title" class="form-control" value="{{ old('title',$project->title) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="4">{{ old('description',$project->description) }}</textarea>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Status *</label>
                    <select name="status" class="form-select" required>
                        @foreach(['planned','active','completed','archived'] as $s)
                            <option value="{{ $s }}" @selected(old('status',$project->status)===$s)>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Progress (0–100)</label>
                    <input type="number" name="progress" min="0" max="100" class="form-control" value="{{ old('progress',$project->progress) }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Start date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ old('start_date',optional($project->start_date)->format('Y-m-d')) }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">End date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ old('end_date',optional($project->end_date)->format('Y-m-d')) }}">
                </div>
            </div>

            <button class="btn btn-primary">Update</button>
            <a href="{{ route('admin.projects.index') }}" class="btn btn-link">Back</a>
        </form>
    </div>
@endsection
