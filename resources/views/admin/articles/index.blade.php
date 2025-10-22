@extends('layouts.admin')
@section('content')
    <div class="container py-4" x-data>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Articles</h1>
            <a href="{{ route('admin.articles.create') }}" class="btn btn-primary">Nouvel article</a>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('admin.articles.index') }}" class="row g-2 align-items-end mb-3" x-ref="filtersForm">
            <div class="col-md-3">
                <label for="q" class="form-label">Recherche</label>
                <input
                    type="text" id="q" name="q" class="form-control"
                    value="{{ request('q') }}" placeholder="Titre, extrait, contenu…"
                    @input.debounce.500ms="$refs.filtersForm.submit()"
                >
            </div>

            <div class="col-md-3">
                <label for="category_id" class="form-label">Catégorie</label>
                <select id="category_id" name="category_id" class="form-select" @change="$refs.filtersForm.submit()">
                    <option value="">Toutes</option>
                    @isset($categories)
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}" @selected((string)$c->id === request('category_id'))>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    @endisset
                </select>
            </div>

            <div class="col-md-2">
                <label for="status" class="form-label">Statut</label>
                <select id="status" name="status" class="form-select" @change="$refs.filtersForm.submit()">
                    <option value="">Tous</option>
                    <option value="published" @selected(request('status')==='published')>Publié</option>
                    <option value="draft" @selected(request('status')==='draft')>Brouillon</option>
                </select>
            </div>

            <div class="col-md-2">
                <label for="from" class="form-label">Du</label>
                <input type="date" id="from" name="from" class="form-control"
                       value="{{ request('from') }}" @change="$refs.filtersForm.submit()">
            </div>

            <div class="col-md-2">
                <label for="to" class="form-label">Au</label>
                <input type="date" id="to" name="to" class="form-control"
                       value="{{ request('to') }}" @change="$refs.filtersForm.submit()">
            </div>

            <div class="col-12 d-flex gap-2 mt-2">
                <noscript><button class="btn btn-outline-primary">Filtrer</button></noscript>
                @php
                    $hasFilters = collect(request()->only(['q','category_id','status','from','to']))->filter()->isNotEmpty();
                @endphp
                @if($hasFilters)
                    <a href="{{ route('admin.articles.index') }}" class="btn btn-outline-secondary">Réinitialiser</a>
                @endif
            </div>
        </form>

        @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

        <div class="row">
            @forelse($articles as $a)
                <div class="col-md-4 mb-3">
                    <div class="card h-100 shadow-sm">
                        @if($a->image_path)
                            <img src="{{ asset('storage/'.$a->image_path) }}" class="card-img-top" alt="{{ $a->title }}">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title mb-1">{{ $a->title }}</h5>
                            <p class="text-muted mb-2"><small>{{ $a->category?->name ?? '—' }}</small></p>
                            <p class="mb-3">{{ Str::limit($a->excerpt ?? strip_tags($a->body), 100) }}</p>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.articles.edit',$a) }}" class="btn btn-sm btn-outline-secondary">Éditer</a>
                                <form method="POST" action="{{ route('admin.articles.destroy',$a) }}">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer ?')">Supprimer</button>
                                </form>
                            </div>
                        </div>
                        <div class="card-footer">
                            <small class="text-muted">
                                {{ $a->is_published ? 'Publié le '.optional($a->published_at)->format('d/m/Y H:i') : 'Brouillon' }}
                            </small>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12"><p class="text-center text-muted">Aucun article.</p></div>
            @endforelse
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted">
                Affichage {{ $articles->firstItem() ?? 0 }}–{{ $articles->lastItem() ?? 0 }} sur {{ $articles->total() }}
            </div>
            {{ $articles->links() }}
        </div>
    </div>
@endsection
