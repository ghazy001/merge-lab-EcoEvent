@extends('layouts.app')
@section('content')
    <div class="container mt-5">
        <h1>Catégorie : {{ $category->name }}</h1>
        <p class="text-muted">{{ $category->description }}</p>
        <div class="row">
            @foreach($articles as $a)
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        @if($a->image_path)
                            <img src="{{ asset('storage/'.$a->image_path) }}" class="card-img-top" alt="{{ $a->title }}">
                        @endif
                        <div class="card-body">
                            <h5>{{ $a->title }}</h5>
                            <a class="btn btn-sm btn-primary" href="{{ route('articles.show',$a) }}">Lire</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        {{ $articles->links() }}
    </div>
@endsection
