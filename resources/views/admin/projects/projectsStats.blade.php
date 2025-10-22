@extends('layouts.admin')
@section('title', 'Projects Stats')

@section('content')
    <div class="container py-4">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="fw-bold m-0">📊 Projects Statistics</h1>
            <a href="{{ route('admin.projects.index') }}" class="btn btn-outline-secondary">Back to Projects</a>
        </div>

        {{-- KPI Cards --}}
        <div class="row g-4 mb-4">
            <div class="col-md-2">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body text-center">
                        <div class="display-6 fw-bold">{{ $totalProjects }}</div>
                        <div class="text-muted">Total</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body text-center">
                        <div class="display-6 fw-bold text-secondary">{{ $plannedCount }}</div>
                        <div class="text-muted">Planned</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body text-center">
                        <div class="display-6 fw-bold text-info">{{ $activeCount }}</div>
                        <div class="text-muted">Active</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body text-center">
                        <div class="display-6 fw-bold text-success">{{ $completedCount }}</div>
                        <div class="text-muted">Completed</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body text-center">
                        <div class="display-6 fw-bold text-warning">{{ $archivedCount }}</div>
                        <div class="text-muted">Archived</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body text-center">
                        <div class="display-6 fw-bold">{{ $avgProgress }}%</div>
                        <div class="text-muted">Avg Progress</div>
                        <div class="small text-muted mt-1">Avg Duration: {{ $avgDurationDays }}d</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts --}}
        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="m-0">Projects Created / Month</h5>
                            <span class="badge bg-light text-dark">Last 12 months</span>
                        </div>
                        <canvas id="projectsCreatedChart" height="130"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="m-0">Projects Completed / Month</h5>
                            <span class="badge bg-light text-dark">Last 12 months</span>
                        </div>
                        <canvas id="projectsCompletedChart" height="130"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top projects by open tasks & Recent projects --}}
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <h5 class="mb-3">Top Projects by Open Tasks</h5>
                        <ul class="list-group list-group-flush">
                            @forelse($topProjectsOpenTasks as $i => $p)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <span class="badge rounded-pill bg-primary me-3" style="width:34px">{{ $i+1 }}</span>
                                        <span class="fw-semibold">{{ $p->title }}</span>
                                        <span class="badge ms-2 {{ $p->status==='active' ? 'bg-info' : ($p->status==='completed' ? 'bg-success' : 'bg-secondary') }}">
                                        {{ $p->status }}
                                    </span>
                                    </div>
                                    <div class="fw-bold">{{ $p->open_tasks_count }}</div>
                                </li>
                            @empty
                                <li class="list-group-item text-muted">No projects found.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <h5 class="mb-3">Recent Projects</h5>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th class="text-end">Progress</th>
                                    <th class="text-end">Start</th>
                                    <th class="text-end">End</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($recentProjects as $p)
                                    <tr>
                                        <td>{{ $p->title }}</td>
                                        <td>
                                        <span class="badge {{ $p->status==='active' ? 'bg-info' : ($p->status==='completed' ? 'bg-success' : ($p->status==='planned' ? 'bg-secondary' : 'bg-warning')) }}">
                                            {{ $p->status }}
                                        </span>
                                        </td>
                                        <td class="text-end">{{ $p->progress }}%</td>
                                        <td class="text-end">{{ optional($p->start_date)->format('d/m/Y') }}</td>
                                        <td class="text-end">{{ optional($p->end_date)->format('d/m/Y') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted">No projects yet.</td></tr>
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
            const labels = @json($labels);
            const created = @json($seriesCreated);
            const completed = @json($seriesCompleted);

            const a = document.getElementById('projectsCreatedChart');
            if (a) new Chart(a, {
                type: 'line',
                data: { labels, datasets: [{ label: 'Created', data: created, borderWidth: 2, tension: .35, pointRadius: 3, fill: true }] },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false }, tooltip: { mode: 'index', intersect: false } },
                    scales: { y: { beginAtZero: true, ticks: { precision: 0 } }, x: { grid: { display: false } } }
                }
            });

            const b = document.getElementById('projectsCompletedChart');
            if (b) new Chart(b, {
                type: 'line',
                data: { labels, datasets: [{ label: 'Completed', data: completed, borderWidth: 2, tension: .35, pointRadius: 3, fill: true }] },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false }, tooltip: { mode: 'index', intersect: false } },
                    scales: { y: { beginAtZero: true, ticks: { precision: 0 } }, x: { grid: { display: false } } }
                }
            });
        })();
    </script>
@endpush
