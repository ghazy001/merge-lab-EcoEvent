@extends('layouts.admin')
@section('content')
    <div class="container py-4" x-data>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Projects</h1>
            <a href="{{ route('admin.projects.create') }}" class="btn btn-primary">New Project</a>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('admin.projects.index') }}" class="row g-2 align-items-end mb-3" x-ref="filtersForm">
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
                    @foreach(($statuses ?? []) as $s)
                        <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">Progress min</label>
                <input type="number" name="min_progress" class="form-control" min="0" max="100"
                       value="{{ request('min_progress') }}" @change="$refs.filtersForm.submit()">
            </div>
            <div class="col-md-2">
                <label class="form-label">Progress max</label>
                <input type="number" name="max_progress" class="form-control" min="0" max="100"
                       value="{{ request('max_progress') }}" @change="$refs.filtersForm.submit()">
            </div>

            <div class="col-md-2">
                <label for="from" class="form-label">Start ≥</label>
                <input type="date" id="from" name="from" class="form-control"
                       value="{{ request('from') }}" @change="$refs.filtersForm.submit()">
            </div>
            <div class="col-md-2">
                <label for="to" class="form-label">End ≤</label>
                <input type="date" id="to" name="to" class="form-control"
                       value="{{ request('to') }}" @change="$refs.filtersForm.submit()">
            </div>

            <div class="col-md-2">
                <label class="form-label">Open tasks min</label>
                <input type="number" name="min_open" class="form-control"
                       value="{{ request('min_open') }}" @change="$refs.filtersForm.submit()">
            </div>
            <div class="col-md-2">
                <label class="form-label">Open tasks max</label>
                <input type="number" name="max_open" class="form-control"
                       value="{{ request('max_open') }}" @change="$refs.filtersForm.submit()">
            </div>

            <div class="col-md-2">
                <label for="sort" class="form-label">Sort</label>
                <select id="sort" name="sort" class="form-select" @change="$refs.filtersForm.submit()">
                    @php $sort = request('sort'); @endphp
                    <option value="created_at_desc" disabled>──</option>
                    <option value="start_desc"      @selected($sort==='start_desc' || $sort===null)>Start ↓</option>
                    <option value="start_asc"       @selected($sort==='start_asc')>Start ↑</option>
                    <option value="end_desc"        @selected($sort==='end_desc')>End ↓</option>
                    <option value="end_asc"         @selected($sort==='end_asc')>End ↑</option>
                    <option value="title_asc"       @selected($sort==='title_asc')>Title A–Z</option>
                    <option value="title_desc"      @selected($sort==='title_desc')>Title Z–A</option>
                    <option value="status_asc"      @selected($sort==='status_asc')>Status A–Z</option>
                    <option value="status_desc"     @selected($sort==='status_desc')>Status Z–A</option>
                    <option value="progress_asc"    @selected($sort==='progress_asc')>Progress ↑</option>
                    <option value="progress_desc"   @selected($sort==='progress_desc')>Progress ↓</option>
                    <option value="open_tasks_asc"  @selected($sort==='open_tasks_asc')>Open tasks ↑</option>
                    <option value="open_tasks_desc" @selected($sort==='open_tasks_desc')>Open tasks ↓</option>
                </select>
            </div>

            <div class="col-md-2">
                <label for="per_page" class="form-label">Per page</label>
                <select id="per_page" name="per_page" class="form-select" @change="$refs.filtersForm.submit()">
                    @foreach([12,24,50,100] as $n)
                        <option value="{{ $n }}" @selected((int)request('per_page',12)===$n)>{{ $n }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12">
                @php
                    $hasFilters = collect(request()->only([
                        'q','status','min_progress','max_progress','from','to','min_open','max_open','sort','per_page'
                    ]))->filter(fn($v)=>$v!==null && $v!=='')->isNotEmpty();
                @endphp
                @if($hasFilters)
                    <a href="{{ route('admin.projects.index') }}" class="btn btn-outline-secondary">Reset</a>
                @endif
                <noscript><button class="btn btn-outline-primary">Apply</button></noscript>
            </div>
        </form>

        @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

        <div class="table-responsive card shadow-sm">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                <tr>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Progress</th>
                    <th>Open tasks</th>
                    <th class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($projects as $p)
                    <tr>
                        <td class="fw-semibold">{{ $p->title }}</td>
                        <td>
                            @php
                                $badge = [
                                    'planned'   => 'bg-secondary',
                                    'active'    => 'bg-primary',
                                    'completed' => 'bg-success',
                                    'archived'  => 'bg-dark',
                                ][$p->status] ?? 'bg-light text-dark';
                            @endphp
                            <span class="badge {{ $badge }}">{{ ucfirst($p->status) }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height:6px;">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: {{ (int)$p->progress }}%;"
                                         aria-valuenow="{{ (int)$p->progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small class="text-muted">{{ (int)$p->progress }}%</small>
                            </div>
                        </td>
                        <td>{{ $p->open_tasks_count }}</td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.projects.edit',$p) }}">Edit</a>
                            <form class="d-inline" method="POST" action="{{ route('admin.projects.destroy',$p) }}">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted">No projects.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted">
                Showing {{ $projects->firstItem() ?? 0 }}–{{ $projects->lastItem() ?? 0 }} of {{ $projects->total() }}
            </div>
            {{ $projects->links() }}
        </div>
    </div>
@endsection
