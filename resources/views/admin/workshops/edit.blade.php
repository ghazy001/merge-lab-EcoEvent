{{-- resources/views/admin/workshops/edit.blade.php --}}
@extends('layouts.admin')
@section('content')
    <div class="container">
        <h1>Éditer Workshop</h1>
        @if($errors->any())
            <div class="alert alert-danger"><ul class="mb-0">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul></div>
        @endif
        <form method="POST" action="{{ route('admin.workshops.update', $workshop) }}">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Titre</label>
                <input name="title" class="form-control" value="{{ old('title',$workshop->title) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description',$workshop->description) }}</textarea>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Début</label>
                    <input type="datetime-local" name="start_at" class="form-control"
                           value="{{ old('start_at', optional($workshop->start_at)->format('Y-m-d\TH:i')) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Fin</label>
                    <input type="datetime-local" name="end_at" class="form-control"
                           value="{{ old('end_at', optional($workshop->end_at)->format('Y-m-d\TH:i')) }}">
                </div>
            </div>
            <div class="row g-3 mt-1">
                <div class="col-md-6">
                    <label class="form-label">Lieu</label>
                    <select name="lieu_id" class="form-select">
                        <option value="">—</option>
                        @foreach($lieux as $l)
                            <option value="{{ $l->id }}" @selected($l->id == old('lieu_id',$workshop->lieu_id))>{{ $l->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Capacité</label>
                    <input type="number" name="capacity" class="form-control" value="{{ old('capacity',$workshop->capacity) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Statut</label>
                    <select name="status" class="form-select">
                        @foreach(['draft'=>'Brouillon','published'=>'Publié'] as $k=>$v)
                            <option value="{{ $k }}" @selected($k==old('status',$workshop->status))>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @include('admin.workshops._materials', ['materials'=>$materials, 'current'=>$current])

            <div class="mt-3">
                <button class="btn btn-primary">Mettre à jour</button>
                <a href="{{ route('admin.workshops.index') }}" class="btn btn-light">Annuler</a>
            </div>
        </form>
    </div>
@endsection

