@extends('layouts.admin')
@section('title', 'Events Stats')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="fw-bold m-0">📊 Events Statistics</h1>
            <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary">Back to Events</a>
        </div>

        {{-- KPI cards --}}
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body text-center">
                        <div class="display-6 fw-bold">{{ $totalEvents }}</div>
                        <div class="text-muted">Total Events</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body text-center">
                        <div class="display-6 fw-bold text-success">{{ $upcomingCount }}</div>
                        <div class="text-muted">Upcoming</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body text-center">
                        <div class="display-6 fw-bold text-info">{{ $pastCount }}</div>
                        <div class="text-muted">Past</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body text-center">
                        <div class="display-6 fw-bold text-warning">{{ $trashedCount }}</div>
                        <div class="text-muted">In Trash</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Capacity row --}}
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body text-center">
                        <div class="display-6 fw-bold">{{ number_format($totalCapacity) }}</div>
                        <div class="text-muted">Total Capacity</div>
                        <div class="small text-muted mt-1">Avg per Event: {{ number_format($avgCapacity) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="m-0">Events per Month</h5>
                            <span class="badge bg-light text-dark">Last 12 months</span>
                        </div>
                        <canvas id="eventsChart" height="120"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top venues + recent events --}}
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <h5 class="mb-3">Top Venues (Upcoming)</h5>
                        <ul class="list-group list-group-flush">
                            @forelse($topVenues as $i => $venue)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <span class="badge rounded-pill bg-primary me-3" style="width:34px">{{ $i+1 }}</span>
                                        <span class="fw-semibold">{{ $venue->name }}</span>
                                    </div>
                                    <div class="fw-bold">{{ $venue->upcoming_count }}</div>
                                </li>
                            @empty
                                <li class="list-group-item text-muted">No venues found.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <h5 class="mb-3">Recent Events</h5>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Venue</th>
                                    <th class="text-end">Start</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($recentEvents as $ev)
                                    <tr>
                                        <td>{{ $ev->title }}</td>
                                        <td>{{ optional($ev->lieu)->name ?? '—' }}</td>
                                        <td class="text-end">{{ \Carbon\Carbon::parse($ev->start_at)->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted">No events yet.</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function () {
            const el = document.getElementById('eventsChart');
            if (!el) return;

            const labels = @json($labels);
            const data   = @json($series);

            new Chart(el, {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: 'Events',
                        data,
                        borderWidth: 2,
                        tension: 0.35,
                        pointRadius: 3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: { mode: 'index', intersect: false }
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 } },
                        x: { grid: { display: false } }
                    }
                }
            });
        })();
    </script>
@endpush
