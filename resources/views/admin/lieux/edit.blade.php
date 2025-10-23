@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1>Modifier Lieu</h1>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('admin.lieux.update', $lieu) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label>Nom</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $lieu->name) }}" required>
            </div>

            <div class="mb-3">
                <label>Adresse</label>
                <input type="text" name="address" class="form-control" value="{{ old('address', $lieu->address) }}">
            </div>

            <div class="mb-3">
                <label>Capacité</label>
                <input type="number" name="capacity" class="form-control" value="{{ old('capacity', $lieu->capacity) }}">
            </div>

            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" class="form-control">{{ old('description', $lieu->description) }}</textarea>
            </div>

            <button class="btn btn-primary">Enregistrer</button>
            <a href="{{ route('admin.lieux.index') }}" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
@endsection
