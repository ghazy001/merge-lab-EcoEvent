{{-- resources/views/admin/workshops/index.blade.php --}}
@extends('layouts.admin')

@section('content')
    <div class="container" x-data>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Workshops</h1>
            <a href="{{ route('admin.workshops.create') }}" class="btn btn-primary btn-sm">Nouveau</a>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('admin.workshops.index') }}" class="row g-2 align-items-end mb-3" x-ref="filtersForm">
            <div class="col-md-3">
                <label for="q" class="form-label">Search</label>
                <input type="text" id="q" name="q" class="form-control"
                       value="{{ request('q') }}" placeholder="Title or description…"
                       @input.debounce.500ms="$refs.filtersForm.submit()">
            </div>

            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select id="status" name="status" class="form-select" @change="$refs.filtersForm.submit()">
                    <option value="">All</option>
                    <option value="draft"     @selected(request('status')==='draft')>Draft</option>
                    <option value="published" @selected(request('status')==='published')>Published</option>
                </select>
            </div>

            <div class="col-md-2">
                <label for="lieu_id" class="form-label">Lieu</label>
                <select id="lieu_id" name="lieu_id" class="form-select" @change="$refs.filtersForm.submit()">
                    <option value="">Tous</option>
                    @foreach(($lieux ?? []) as $l)
                        <option value="{{ $l->id }}" @selected(request('lieu_id')==$l->id)>{{ $l->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label for="period" class="form-label">Période rapide</label>
                <select id="period" name="period" class="form-select" @change="$refs.filtersForm.submit()">
                    @php $p = request('period'); @endphp
                    <option value="">Toutes</option>
                    <option value="upcoming" @selected($p==='upcoming')>À venir</option>
                    <option value="past"     @selected($p==='past')>Passés</option>
                    <option value="today"    @selected($p==='today')>Aujourd’hui</option>
                    <option value="week"     @selected($p==='week')>Cette semaine</option>
                    <option value="month"    @selected($p==='month')>Ce mois</option>
                </select>
            </div>

            <div class="col-md-2">
                <label for="from" class="form-label">Du (start)</label>
                <input type="date" id="from" name="from" class="form-control"
                       value="{{ request('from') }}" @change="$refs.filtersForm.submit()">
            </div>
            <div class="col-md-2">
                <label for="to" class="form-label">Au (start)</label>
                <input type="date" id="to" name="to" class="form-control"
                       value="{{ request('to') }}" @change="$refs.filtersForm.submit()">
            </div>

            <div class="col-md-2">
                <label class="form-label">Capacité min</label>
                <input type="number" name="min_capacity" class="form-control"
                       value="{{ request('min_capacity') }}" @change="$refs.filtersForm.submit()">
            </div>
            <div class="col-md-2">
                <label class="form-label">Capacité max</label>
                <input type="number" name="max_capacity" class="form-control"
                       value="{{ request('max_capacity') }}" @change="$refs.filtersForm.submit()">
            </div>

            <div class="col-md-3">
                <label for="material_id" class="form-label">Material</label>
                <select id="material_id" name="material_id" class="form-select" @change="$refs.filtersForm.submit()">
                    <option value="">Any</option>
                    @foreach(($materials ?? []) as $m)
                        <option value="{{ $m->id }}" @selected(request('material_id')==$m->id)>{{ $m->name }}</option>
                    @endforeach
                </select>
            </div>
            {{-- Or enable multi:
            <div class="col-md-3">
                <label class="form-label">Materials (any)</label>
                <select name="material_ids[]" class="form-select" multiple @change="$refs.filtersForm.submit()">
                    @foreach(($materials ?? []) as $m)
                        <option value="{{ $m->id }}" @selected(collect(request('material_ids',[]))->contains($m->id))>{{ $m->name }}</option>
                    @endforeach
                </select>
            </div>
            --}}

            <div class="col-md-2">
                <label for="sort" class="form-label">Tri</label>
                <select id="sort" name="sort" class="form-select" @change="$refs.filtersForm.submit()">
                    @php $sort = request('sort'); @endphp
                    <option value="start_desc" @selected($sort==='start_desc' || $sort===null)>Début ↓</option>
                    <option value="start_asc"  @selected($sort==='start_asc')>Début ↑</option>
                    <option value="end_desc"   @selected($sort==='end_desc')>Fin ↓</option>
                    <option value="end_asc"    @selected($sort==='end_asc')>Fin ↑</option>
                    <option value="title_asc"  @selected($sort==='title_asc')>Titre A–Z</option>
                    <option value="title_desc" @selected($sort==='title_desc')>Titre Z–A</option>
                    <option value="capacity_asc"  @selected($sort==='capacity_asc')>Capacité ↑</option>
                    <option value="capacity_desc" @selected($sort==='capacity_desc')>Capacité ↓</option>
                    <option value="status_asc"    @selected($sort==='status_asc')>Statut A–Z</option>
                    <option value="status_desc"   @selected($sort==='status_desc')>Statut Z–A</option>
                </select>
            </div>

            <div class="col-md-2">
                <label for="per_page" class="form-label">/page</label>
                <select id="per_page" name="per_page" class="form-select" @change="$refs.filtersForm.submit()">
                    @foreach([10,15,25,50] as $n)
                        <option value="{{ $n }}" @selected(request('per_page',10)==$n)>{{ $n }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12">
                @php
                    $hasFilters = collect(request()->only([
                        'q','status','lieu_id','period','from','to','min_capacity','max_capacity','material_id','sort','per_page'
                    ]))->filter(fn($v)=>$v!==null && $v!=='')->isNotEmpty();
                @endphp
                @if($hasFilters)
                    <a href="{{ route('admin.workshops.index') }}" class="btn btn-outline-secondary">Réinitialiser</a>
                @endif
                <noscript><button class="btn btn-outline-primary">Filtrer</button></noscript>
            </div>
        </form>

        @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

        {{-- Table --}}
        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead>
                <tr>
                    <th>#</th><th>Titre</th><th>Date</th><th>Lieu</th><th>Statut</th><th class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($workshops as $w)
                    <tr>
                        <td>{{ $w->id }}</td>
                        <td>
                            <div class="fw-semibold">{{ $w->title }}</div>
                            @if($w->materials->isNotEmpty())
                                <small class="text-muted">
                                    Matériaux:
                                    {{ $w->materials->pluck('name')->join(', ') }}
                                </small>
                            @endif
                        </td>
                        <td>
                            {{ optional($w->start_at)->format('d/m/Y H:i') }}
                            @if($w->end_at)
                                <span class="text-muted">→ {{ $w->end_at->format('d/m/Y H:i') }}</span>
                            @endif
                        </td>
                        <td>{{ $w->lieu?->name ?? '—' }}</td>
                        <td>
                        <span class="badge {{ $w->status === 'published' ? 'bg-success' : 'bg-secondary' }}">
                            {{ ucfirst($w->status) }}
                        </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.workshops.edit',$w) }}" class="btn btn-sm btn-outline-secondary">Éditer</a>
                            <form action="{{ route('admin.workshops.destroy',$w) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Supprimer ce workshop ?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">Aucun workshop trouvé.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted">
                Affichage {{ $workshops->firstItem() ?? 0 }}–{{ $workshops->lastItem() ?? 0 }} sur {{ $workshops->total() }}
            </div>
            {{ $workshops->links() }}
        </div>
    </div>
@endsection
