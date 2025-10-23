@extends('layouts.app')
@section('title','Causes')

@section('content')
    <div class="container py-5">
        @include('partials.flash') {{-- session messages --}}

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mb-0">Causes</h1>
        </div>

        <div class="row g-4">
            @foreach($causes as $cause)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm rounded-4 overflow-hidden">

                        {{-- IMAGE --}}
                        @if($cause->image_path)
                            <img
                                src="{{ asset('storage/'.$cause->image_path) }}"
                                alt="{{ $cause->title }}"
                                class="card-img-top"
                                style="height: 180px; object-fit: cover;"
                            >
                        @endif

                        <div class="card-body d-flex flex-column">
                            <h4 class="card-title mb-2">
                                <a href="{{ route('causes.show', $cause) }}" class="text-decoration-none">
                                    {{ $cause->title }}
                                </a>
                            </h4>

                            <p class="card-text text-muted mb-2">
                                {{ Str::limit($cause->description, 140) }}
                            </p>

                            {{-- Goal & Donations --}}
                            <p class="mb-1">
                                <strong>Goal:</strong>
                                €{{ number_format($cause->goal_amount, 2) }}
                            </p>

                            <p class="mb-2">
                                <strong>Donations:</strong>
                                €{{ number_format($cause->totalDonations(), 2) }}
                            </p>

                            <p class="mb-2">
                                <strong>People donated:</strong>
                                {{ $cause->donations_count }}
                            </p>

                            @php $progress = $cause->percentRaised(); @endphp
                            <div class="progress mb-3" style="height: 18px;">
                                <div
                                    class="progress-bar
                                    @if($progress >= 100) bg-success
                                    @elseif($progress >= 50) bg-info
                                    @else bg-warning
                                    @endif"
                                    role="progressbar"
                                    style="width: {{ min($progress, 100) }}%">
                                    {{ $progress }}%
                                </div>
                            </div>

                            {{-- Status Badge --}}
                            <p class="mt-auto mb-0">
                                <span class="badge bg-{{ $cause->status === 'active' ? 'success' : ($cause->status === 'completed' ? 'secondary' : 'danger') }}">
                                    {{ ucfirst($cause->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-4 d-flex justify-content-center">
            {{ $causes->links() }}
        </div>
    </div>
@endsection
