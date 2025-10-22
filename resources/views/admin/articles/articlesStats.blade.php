@extends('layouts.admin')

@section('title', 'Articles Stats')

@section('content')
    <div class="container py-4">

        <h1 class="mb-4 fw-bold">📊 Articles Statistics</h1>

        {{-- KPI Cards --}}
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body text-center">
                        <div class="display-6 fw-bold">{{ $totalArticles }}</div>
                        <div class="text-muted">Total Articles</div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body text-center">
                        <div class="display-6 fw-bold text-success">{{ $publishedCount }}</div>
                        <div class="text-muted">Published</div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body text-center">
                        <div class="display-6 fw-bold text-warning">{{ $draftCount }}</div>
                        <div class="text-muted">Drafts</div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body text-center">
                        <div class="display-6 fw-bold">{{ $avgPerCategory }}</div>
                        <div class="text-muted">Avg / Category</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Chart + Top Categories --}}
        <div class="row g-4 mb-4">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="m-0">Articles Published per Month</h5>
                            <span class="badge bg-light text-dark">Last 12 months</span>
                        </div>
                        <canvas id="articlesChart" height="150"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <h5 class="mb-3">Top Categories</h5>
                        <ul class="list-group list-group-flush">
                            @forelse($topCategories as $i => $category)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <span class="badge rounded-pill bg-primary me-3" style="width:34px">{{ $i+1 }}</span>
                                        <span class="fw-semibold">{{ $category->name }}</span>
                                    </div>
                                    <div class="fw-bold">{{ $category->articles_count }}</div>
                                </li>
                            @empty
                                <li class="list-group-item text-muted">No categories found.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Articles --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body">
                <h5 class="mb-3">Recent Published Articles</h5>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                        <tr>
                            <th>Title</th>
                            <th class="text-end">Published At</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($recentArticles as $article)
                            <tr>
                                <td>{{ $article->title }}</td>
                                <td class="text-end">{{ $article->published_at?->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-center text-muted">No articles yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('articlesChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($labels),
                    datasets: [{
                        label: 'Articles Published',
                        data: @json($series),
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
        }
    </script>
@endpush
