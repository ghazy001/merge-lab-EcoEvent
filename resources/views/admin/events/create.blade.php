@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1>Nouveau Événement</h1>

        <!-- Validation Errors -->
        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.events.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label>Titre</label>
                <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
            </div>

            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" class="form-control">{{ old('description') }}</textarea>
            </div>

            <div class="mb-3">
                <label>Début</label>
                <input type="datetime-local" name="start_at" class="form-control" value="{{ old('start_at') }}" required>
                <small class="form-text text-muted">
                    Format HTML input, Laravel accepte 'YYYY-MM-DD HH:MM:SS' — convertir côté controller si besoin.
                </small>
            </div>

            <div class="mb-3">
                <label>Fin (optionnel)</label>
                <input type="datetime-local" name="end_at" class="form-control" value="{{ old('end_at') }}">
            </div>

            <div class="mb-3">
                <label>Lieu</label>
                <select name="lieu_id" class="form-control" required>
                    <option value="">-- Choisir un lieu --</option>
                    @foreach($lieux as $id => $name)
                        <option value="{{ $id }}" {{ old('lieu_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label>Capacité</label>
                <input type="number" name="capacity" class="form-control" value="{{ old('capacity') }}">
            </div>

            <button class="btn btn-primary">Créer</button>
            <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
@endsection
