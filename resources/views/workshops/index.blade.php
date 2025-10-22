{{-- resources/views/workshops/index.blade.php --}}
@extends('layouts.app')
@section('content')
    <div class="container mt-5">
        <h1>Workshops</h1>
        <div class="row">
            @foreach($workshops as $w)
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5>{{ $w->title }}</h5>
                            <p class="mb-1">{{ optional($w->start_at)->format('d/m/Y H:i') }}</p>
                            <p class="text-muted mb-2">{{ $w->lieu?->name }}</p>
                            <a href="{{ route('workshops.show',$w) }}" class="btn btn-sm btn-primary">Voir</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        {{ $workshops->links() }}
    </div>
@endsection

