@extends('layouts.app')

@section('content')
    <div class="container" style="max-width: 480px">
        <h1 class="mb-4">Inscription</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ url('/register') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Nom</label>
                <input name="name" type="text" class="form-control" value="{{ old('name') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input name="email" type="email" class="form-control" value="{{ old('email') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Mot de passe</label>
                <input name="password" type="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Confirmer le mot de passe</label>
                <input name="password_confirmation" type="password" class="form-control" required>
            </div>
            <button class="btn btn-success w-100">Créer le compte</button>
        </form>

        <p class="mt-3 text-center">
            Déjà inscrit ? <a href="{{ route('login') }}">Se connecter</a>
        </p>
    </div>
@endsection
