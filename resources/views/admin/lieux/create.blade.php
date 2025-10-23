@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1>Nouveau lieu</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.lieux.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Nom</label>
                <input type="text" name="name" class="form-control" id="name" value="{{ old('name') }}">
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">Adresse</label>
                <input type="text" name="address" class="form-control" id="address" value="{{ old('address') }}">
            </div>

            <div class="mb-3">
                <label for="capacity" class="form-label">Capacité</label>
                <input type="number" name="capacity" class="form-control" id="capacity" value="{{ old('capacity') }}">
            </div>

            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="{{ route('admin.lieux.index') }}" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
@endsection
