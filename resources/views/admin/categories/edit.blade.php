@extends('layouts.admin')
@section('content')
    <div class="container py-4">
        <h1>Éditer : {{ $category->name }}</h1>

        @if($errors->any())
            <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif

        <form method="POST" action="{{ route('admin.categories.update',$category) }}" class="card p-3 shadow-sm">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Nom *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name',$category->name) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description',$category->description) }}</textarea>
            </div>
            <button class="btn btn-primary">Mettre à jour</button>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-link">Retour</a>
        </form>
    </div>
@endsection
