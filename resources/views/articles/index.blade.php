@extends('layouts.app')
@section('content')
    <div class="container mt-5">
        <h1 class="mb-4">Articles</h1>
        <div class="row">
            @forelse($articles as $a)
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        @if($a->image_path)
                            <img src="{{ asset('storage/'.$a->image_path) }}" class="card-img-top" alt="{{ $a->title }}">
                        @endif
                        <div class="card-body">
                            <h5>{{ $a->title }}</h5>
                            <p class="text-muted"><small>{{ $a->category?->name ?? '—' }}</small></p>
                            <p>{{ Str::limit($a->excerpt ?? strip_tags($a->body), 110) }}</p>
                            <a class="btn btn-sm btn-primary" href="{{ route('articles.show',$a) }}">Lire</a>
                        </div>
                        <div class="card-footer">
                            <small class="text-muted">{{ optional($a->published_at)->format('d/m/Y') }}</small>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-muted">Aucun article publié.</p>
            @endforelse
        </div>
        {{ $articles->links() }}
    </div>
@endsection
