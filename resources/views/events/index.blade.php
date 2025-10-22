@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4">Événements</h1>

        @if($events->isEmpty())
            <p>Aucun événement disponible pour le moment.</p>
        @else
            <div class="row">
                @foreach($events as $event)
                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">{{ $event->title }}</h5>
                                @if($event->start_at)
                                    <p class="card-text">
                                        <strong>Date début:</strong> {{ \Carbon\Carbon::parse($event->start_at)->format('d/m/Y H:i') }}
                                    </p>
                                @endif
                                @if($event->end_at)
                                    <p class="card-text">
                                        <strong>Date fin:</strong> {{ \Carbon\Carbon::parse($event->end_at)->format('d/m/Y H:i') }}
                                    </p>
                                @endif
                                @if($event->lieu)
                                    <p class="card-text"><small>Lieu: {{ $event->lieu->name }}</small></p>
                                @endif

                                <a href="{{ route('events.show', $event) }}" class="btn btn-sm btn-primary mt-auto">Voir</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $events->links() }}
            </div>
        @endif
    </div>
@endsection
