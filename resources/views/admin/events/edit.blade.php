@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1>Modifier Événement</h1>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('admin.events.update', $event) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label>Titre</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $event->title) }}" required>
            </div>

            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" class="form-control">{{ old('description', $event->description) }}</textarea>
            </div>

            <div class="mb-3">
                <label>Début</label>
                <input type="datetime-local" name="start_at" class="form-control"
                       value="{{ old('start_at', \Carbon\Carbon::parse($event->start_at)->format('Y-m-d\TH:i')) }}" required>
            </div>

            <div class="mb-3">
                <label>Fin</label>
                <input type="datetime-local" name="end_at" class="form-control"
                       value="{{ old('end_at', optional($event->end_at) ? \Carbon\Carbon::parse($event->end_at)->format('Y-m-d\TH:i') : '') }}">
            </div>

            <div class="mb-3">
                <label>Lieu</label>
                <select name="lieu_id" class="form-control" required>
                    @foreach($lieux as $id => $name)
                        <option value="{{ $id }}" {{ (old('lieu_id', $event->lieu_id) == $id) ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label>Capacité</label>
                <input type="number" name="capacity" class="form-control" value="{{ old('capacity', $event->capacity) }}">
            </div>

            <button class="btn btn-primary">Enregistrer</button>
            <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
@endsection
