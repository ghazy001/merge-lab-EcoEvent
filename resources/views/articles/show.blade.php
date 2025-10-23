@extends('layouts.app')
@section('title', $article->title)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/article-show.css') }}">
@endpush

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Article Header -->
                <article class="article-content">
                    <!-- Category and Date Badge -->
                    <div class="mb-4">
                        @if($article->category)
                            <span class="badge bg-primary me-2 px-3 py-2">{{ $article->category->name }}</span>
                        @endif
                        @if($article->published_at)
                            <span class="text-muted">
                            <i class="bi bi-calendar3"></i>
                            {{ $article->published_at->format('F d, Y') }}
                            <span class="mx-2">•</span>
                            <i class="bi bi-clock"></i>
                            {{ $article->published_at->format('H:i') }}
                        </span>
                        @endif
                    </div>

                    <!-- Title -->
                    <h1 class="display-4 fw-bold mb-4 text-dark">{{ $article->title }}</h1>

                    <!-- Excerpt -->
                    @if($article->excerpt)
                        <p class="lead text-secondary fs-5 mb-4 border-start border-4 border-primary ps-3">
                            {{ $article->excerpt }}
                        </p>
                    @endif

                    <!-- Featured Image -->
                    @if($article->image_path)
                        <div class="mb-5">
                            <img src="{{ asset('storage/'.$article->image_path) }}"
                                 class="img-fluid rounded shadow-lg w-100"
                                 alt="{{ $article->title }}"
                                 style="max-height: 600px; object-fit: cover;">
                        </div>
                    @endif

                    <!-- Article Body -->
                    <div class="article-body fs-5 lh-lg text-dark">
                        {!! nl2br(e($article->body)) !!}
                    </div>

                    <!-- Article Footer -->
                    <div class="mt-5 pt-4 border-top">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div class="text-muted">
                                <small>
                                    <i class="bi bi-person-circle"></i>
                                    Published by {{ $article->user->name ?? 'Admin' }}
                                </small>
                            </div>
                            <div>
                                <a href="{{ route('articles.index') }}" class="btn btn-outline-primary">
                                    <i class="bi bi-arrow-left"></i> Back to Articles
                                </a>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </div>
@endsection
