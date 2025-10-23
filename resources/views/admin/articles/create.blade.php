@extends('layouts.admin')
@section('content')
    <div class="container py-4">
        <h1>Nouvel article</h1>

        @if($errors->any())
            <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif

        <form method="POST" action="{{ route('admin.articles.store') }}" enctype="multipart/form-data" class="card p-3 shadow-sm">
            @csrf
            <div class="mb-3">
                <label class="form-label">Titre *</label>
                <input name="title" class="form-control" value="{{ old('title') }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Catégorie</label>
                <select name="category_id" class="form-select">
                    <option value="">—</option>
                    @foreach($categories as $id => $name)
                        <option value="{{ $id }}" @selected(old('category_id')==$id)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Résumé (excerpt)</label>
                <input name="excerpt" class="form-control" value="{{ old('excerpt') }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Contenu *</label>
                <textarea name="body" rows="8" class="form-control" required>{{ old('body') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Image</label>
                <input type="file" name="image" class="form-control">
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" name="is_published" value="1" class="form-check-input" id="pub" @checked(old('is_published'))>
                <label class="form-check-label" for="pub">Publier maintenant</label>
            </div>

            <button class="btn btn-primary">Enregistrer</button>
            <a href="{{ route('admin.articles.index') }}" class="btn btn-link">Annuler</a>
        </form>
    </div>
@endsection
