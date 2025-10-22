@extends('layouts.app')
@section('content')
    <div class="container mt-5">
        <h1 class="mb-4">Projects</h1>
        <div class="row">
            @forelse($projects as $p)
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="mb-1">{{ $p->title }}</h5>
                            <p class="text-muted mb-2"><small>Status: {{ $p->status }} • {{ $p->progress }}%</small></p>
                            <p>{{ Str::limit($p->description, 120) }}</p>
                            <a class="btn btn-sm btn-primary" href="{{ route('projects.show',$p) }}">Open</a>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-muted">No projects.</p>
            @endforelse
        </div>
        {{ $projects->links() }}
    </div>
@endsection
