{{-- resources/views/admin/causes/index.blade.php --}}
@extends('layouts.admin')
@section('title','Manage Causes')

@section('content')
    <div class="container" x-data>
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="mb-0">Causes</h1>
            <a href="{{ route('admin.causes.create') }}" class="btn btn-primary">New Cause</a>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('admin.causes.index') }}" class="row g-2 align-items-end mt-3 mb-3" x-ref="filtersForm">
            <div class="col-md-3">
                <label for="q" class="form-label">Search</label>
                <input type="text" id="q" name="q" class="form-control"
                       value="{{ request('q') }}"
                       placeholder="Title or description…"
                       @input.debounce.500ms="$refs.filtersForm.submit()">
            </div>

            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select id="status" name="status" class="form-select" @change="$refs.filtersForm.submit()">
                    <option value="">All</option>
                    @foreach($statuses as $s)
                        <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">Goal (min)</label>
                <input type="number" step="0.01" name="min_goal" class="form-control"
                       value="{{ request('min_goal') }}" @change="$refs.filtersForm.submit()">
            </div>

            <div class="col-md-2">
                <label class="form-label">Goal (max)</label>
                <input type="number" step="0.01" name="max_goal" class="form-control"
                       value="{{ request('max_goal') }}" @change="$refs.filtersForm.submit()">
            </div>

            <div class="col-md-1 form-check mt-4 ms-2">
                <input type="checkbox" id="has_image" name="has_image" value="1" class="form-check-input"
                       @change="$refs.filtersForm.submit()" {{ request()->boolean('has_image') ? 'checked' : '' }}>
                <label for="has_image" class="form-check-label">With image</label>
            </div>

            <div class="col-md-2">
                <label for="sort" class="form-label">Sort</label>
                <select id="sort" name="sort" class="form-select" @change="$refs.filtersForm.submit()">
                    @php $sort = request('sort'); @endphp
                    <option value="created_desc" @selected($sort==='created_desc' || $sort===null)>Newest</option>
                    <option value="created_asc"  @selected($sort==='created_asc')>Oldest</option>
                    <option value="title_asc"    @selected($sort==='title_asc')>Title A–Z</option>
                    <option value="title_desc"   @selected($sort==='title_desc')>Title Z–A</option>
                    <option value="goal_asc"     @selected($sort==='goal_asc')>Goal low→high</option>
                    <option value="goal_desc"    @selected($sort==='goal_desc')>Goal high→low</option>
                    <option value="status_asc"   @selected($sort==='status_asc')>Status A–Z</option>
                    <option value="status_desc"  @selected($sort==='status_desc')>Status Z–A</option>
                </select>
            </div>

            <div class="col-md-2">
                <label for="per_page" class="form-label">Per page</label>
                <select id="per_page" name="per_page" class="form-select" @change="$refs.filtersForm.submit()">
                    @foreach([10,15,25,50] as $n)
                        <option value="{{ $n }}" @selected(request('per_page',10)==$n)>{{ $n }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 d-flex gap-2">
                <noscript>
                    <button class="btn btn-outline-primary">Apply</button>
                </noscript>
                @php $hasFilters = collect(request()->only(['q','status','min_goal','max_goal','has_image','sort','per_page']))
                ->filter(fn($v)=>$v!==null && $v!=='')->isNotEmpty(); @endphp
                @if($hasFilters)
                    <a href="{{ route('admin.causes.index') }}" class="btn btn-outline-secondary">Reset</a>
                @endif
            </div>
        </form>

        @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

        <div class="table-responsive">
            <table class="table mt-3 align-middle">
                <thead>
                <tr>
                    <th>#</th>
                    <th style="width:80px">Image</th>
                    <th>Title</th>
                    <th>Status</th>
                    <th class="text-end">Goal</th>
                    <th class="text-end">Raised</th>
                    <th style="width:240px">Progress</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($causes as $cause)
                    <tr>
                        <td>{{ $cause->id }}</td>
                        <td>
                            @if($cause->image_path)
                                <img src="{{ asset('storage/'.$cause->image_path) }}" alt="" class="rounded"
                                     style="height:48px;width:48px;object-fit:cover">
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="fw-medium">{{ $cause->title }}</td>
                        <td>
                            @php
                                $badge = [
                                    'active' => 'bg-success',
                                    'completed' => 'bg-primary',
                                    'canceled' => 'bg-secondary'
                                ][$cause->status] ?? 'bg-light text-dark';
                            @endphp
                            <span class="badge {{ $badge }}">{{ ucfirst($cause->status) }}</span>
                        </td>
                        <td class="text-end">€{{ number_format($cause->goal_amount, 2) }}</td>
                        <td class="text-end">€{{ number_format($cause->total_donations, 2) }}</td>
                        <td>
                            <div class="progress" style="height:8px;">
                                <div class="progress-bar" style="width: {{ $cause->percentRaised() }}%;"></div>
                            </div>
                            <small class="text-muted">{{ $cause->percent_raised }}%</small>
                        </td>
                        <td>
                            <a href="{{ route('admin.causes.edit', $cause) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('admin.causes.destroy', $cause) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted">No causes found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination (keeps filters via withQueryString) --}}
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted">
                Showing {{ $causes->firstItem() ?? 0 }}–{{ $causes->lastItem() ?? 0 }} of {{ $causes->total() }}
            </div>
            {{ $causes->links() }}
        </div>
    </div>
@endsection
