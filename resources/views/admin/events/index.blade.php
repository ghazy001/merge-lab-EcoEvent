@extends('layouts.admin')

@section('content')
    <div class="container" x-data>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="mb-0">Événements</h1>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.events.create') }}" class="btn btn-primary">Nouvel événement</a>
            </div>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('admin.events.index') }}" class="row g-2 align-items-end mb-3" x-ref="filtersForm">
            <div class="col-md-3">
                <label for="q" class="form-label">Recherche</label>
                <input type="text" id="q" name="q" class="form-control"
                       value="{{ request('q') }}" placeholder="Titre ou description…"
                       @input.debounce.500ms="$refs.filtersForm.submit()">
            </div>

            <div class="col-md-2">
                <label for="lieu_id" class="form-label">Lieu</label>
                <select id="lieu_id" name="lieu_id" class="form-select" @change="$refs.filtersForm.submit()">
                    <option value="">Tous</option>
                    @foreach($lieux as $lieu)
                        <option value="{{ $lieu->id }}" @selected(request('lieu_id')==$lieu->id)>{{ $lieu->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label for="period" class="form-label">Période rapide</label>
                <select id="period" name="period" class="form-select" @change="$refs.filtersForm.submit()">
                    @php $p = request('period'); @endphp
                    <option value="">Toutes</option>
                    <option value="upcoming" @selected($p==='upcoming')>À venir</option>
                    <option value="past" @selected($p==='past')>Passés</option>
                    <option value="today" @selected($p==='today')>Aujourd’hui</option>
                    <option value="week" @selected($p==='week')>Cette semaine</option>
                    <option value="month" @selected($p==='month')>Ce mois</option>
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

            <div class="col-md-1">
                <label for="min_capacity" class="form-label">Cap. min</label>
                <input type="number" id="min_capacity" name="min_capacity" class="form-control"
                       value="{{ request('min_capacity') }}" @change="$refs.filtersForm.submit()">
            </div>

            <div class="col-md-1">
                <label for="max_capacity" class="form-label">Cap. max</label>
                <input type="number" id="max_capacity" name="max_capacity" class="form-control"
                       value="{{ request('max_capacity') }}" @change="$refs.filtersForm.submit()">
            </div>

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
                </select>
            </div>

            <div class="col-md-1">
                <label for="per_page" class="form-label">/page</label>
                <select id="per_page" name="per_page" class="form-select" @change="$refs.filtersForm.submit()">
                    @foreach([10,15,25,50] as $n)
                        <option value="{{ $n }}" @selected(request('per_page',15)==$n)>{{ $n }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12">
                @php
                    $hasFilters = collect(request()->only(['q','lieu_id','period','from','to','min_capacity','max_capacity','sort','per_page']))
                        ->filter(fn($v)=>$v!==null && $v!=='')->isNotEmpty();
                @endphp
                @if($hasFilters)
                    <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary">Réinitialiser</a>
                @endif
                <noscript><button class="btn btn-outline-primary">Filtrer</button></noscript>
            </div>
        </form>

        {{-- Flash --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Table --}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                <tr>
                    <th>Titre</th>
                    <th>Description</th>
                    <th>Début</th>
                    <th>Fin</th>
                    <th>Lieu</th>
                    <th class="text-end">Capacité</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($events as $event)
                    <tr>
                        <td class="fw-semibold">{{ $event->title }}</td>
                        <td>{{ Str::limit($event->description, 60) }}</td>
                        <td>{{ optional($event->start_at)->format('d/m/Y H:i') }}</td>
                        <td>{{ optional($event->end_at)->format('d/m/Y H:i') }}</td>
                        <td>{{ $event->lieu->name ?? '—' }}</td>
                        <td class="text-end">{{ $event->capacity ?? '—' }}</td>
                        <td>
                            <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-sm btn-secondary">Modifier</a>
                            <form action="{{ route('admin.events.destroy', $event) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Supprimer cet événement ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Suppr</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted">Aucun événement trouvé.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination w/ counters --}}
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted">
                Affichage {{ $events->firstItem() ?? 0 }}–{{ $events->lastItem() ?? 0 }} sur {{ $events->total() }}
            </div>
            {{ $events->links() }}
        </div>
    </div>
@endsection
