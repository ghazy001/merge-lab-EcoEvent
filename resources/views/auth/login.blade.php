@extends('layouts.app')

@section('content')
    <div class="container" style="max-width: 480px">
        <h1 class="mb-4">Connexion</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ url('/login') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input name="email" type="email" class="form-control" value="{{ old('email') }}" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Mot de passe</label>
                <input name="password" type="password" class="form-control" required>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label" for="remember">Se souvenir de moi</label>
            </div>
            <button class="btn btn-primary w-100">Se connecter</button>
        </form>

        <p class="mt-3 text-center">
            Pas de compte ? <a href="{{ route('register') }}">S’inscrire</a>
        </p>
    </div>
@endsection
