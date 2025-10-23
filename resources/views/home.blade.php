@extends('layouts.app')

@section('title', 'Environs - Home')

@section('content')

    {{-- Carousel Start --}}
    <div class="container-fluid carousel-header vh-100 px-0">
        <div id="carouselId" class="carousel slide" data-bs-ride="carousel">
            <ol class="carousel-indicators">
                <li data-bs-target="#carouselId" data-bs-slide-to="0" class="active"></li>
                <li data-bs-target="#carouselId" data-bs-slide-to="1"></li>
                <li data-bs-target="#carouselId" data-bs-slide-to="2"></li>
            </ol>
            <div class="carousel-inner" role="listbox">
                <div class="carousel-item active">
                    <img src="{{ asset('img/carousel-1.jpg') }}" class="img-fluid" alt="Image">
                    <div class="carousel-caption">
                        <div class="p-3" style="max-width: 900px;">
                            <h4 class="text-white text-uppercase fw-bold mb-4" style="letter-spacing: 3px;">WE'll Save Our Planet</h4>
                            <h1 class="display-1 text-capitalize text-white mb-4">Protect Environment</h1>
                            <p class="mb-5 fs-5">Every action counts in the fight against climate change. From organizing eco-friendly events to supporting green causes, we empower communities to make sustainable choices. Be part of the solution and help us build a cleaner, greener world for all.</p>

                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="{{ asset('img/carousel-2.jpg') }}" class="img-fluid" alt="Image">
                    <div class="carousel-caption">
                        <div class="p-3" style="max-width: 900px;">
                            <h4 class="text-white text-uppercase fw-bold mb-4">WE'll Save Our Planet</h4>
                            <h1 class="display-1 text-capitalize text-white mb-4">Protect Environment</h1>
                            <p class="mb-5 fs-5">Join us in creating a sustainable future through community action and environmental awareness. Together, we organize impactful events, support vital causes, and empower individuals to make a real difference in protecting our planet for generations to come.</p>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="{{ asset('img/carousel-3.jpg') }}" class="img-fluid" alt="Image">
                    <div class="carousel-caption">
                        <div class="p-3" style="max-width: 900px;">
                            <h4 class="text-white text-uppercase fw-bold mb-4">WE'll Save Our Planet</h4>
                            <h1 class="display-1 text-capitalize text-white mb-4">Protect Environment</h1>
                            <p class="mb-5 fs-5">Every small action leads to big change. Through workshops, conservation projects, and eco-friendly events, we're building a global community dedicated to environmental protection and sustainable living.</p>

                        </div>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselId" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselId" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>
    {{-- Carousel End --}}




    {{-- Causes Section Start --}}
    <section class="causes-hero py-5 bg-light">
        <div class="container text-center py-5">
            <!-- Title -->
            <h2 class="display-5 fw-bold mb-3">Support Our Causes</h2>
            <p class="lead mb-5">Join hands to make a difference. Explore our causes and help create a better future.</p>

            <!-- Animated button -->
            <a href="{{ route('causes.index') }}"
               class="btn btn-primary btn-lg rounded-pill px-5 py-3 fw-bold shadow-sm position-relative overflow-hidden"
               style="transition: all 0.4s;">
                <span class="position-relative">See Our Causes</span>
                <span class="position-absolute top-0 start-0 w-100 h-100 bg-white opacity-10 rounded-pill"
                      style="transform: translateX(-100%); transition: transform 0.4s;"></span>
            </a>

            <!-- Featured Causes Cards -->
            <div class="row justify-content-center mt-5 g-4">
                @foreach($featuredCauses as $cause)
                    @php
                        $progress = method_exists($cause, 'percentRaised') ? $cause->percentRaised() : ($cause->donations_percent ?? 0);
                        $clamped = min(max((int)$progress, 0), 100);
                    @endphp

                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                            {{-- IMAGE --}}
                            @if($cause->image_path)
                                <img
                                    src="{{ asset('storage/'.$cause->image_path) }}"
                                    alt="{{ $cause->title }}"
                                    class="card-img-top"
                                    style="height: 180px; object-fit: cover;"
                                >
                            @endif

                            <div class="card-body text-center d-flex flex-column">
                                <h4 class="mb-2">
                                    <a href="{{ route('causes.show', $cause) }}" class="text-decoration-none">
                                        {{ $cause->title }}
                                    </a>
                                </h4>

                                <p class="card-text text-muted mb-3">
                                    {{ Str::limit($cause->description, 80) }}
                                </p>

                                {{-- GOAL / RAISED --}}
                                <p class="mb-2 small">
                                    <strong>Raised:</strong> €{{ number_format($cause->totalDonations(), 2) }}
                                    <span class="text-muted">/</span>
                                    <strong>Goal:</strong> €{{ number_format($cause->goal_amount, 2) }}
                                </p>

                                {{-- PROGRESS BAR --}}
                                <div class="progress mb-3" style="height: 14px;">
                                    <div
                                        class="progress-bar
                            @if($clamped >= 100) bg-success
                            @elseif($clamped >= 50) bg-info
                            @else bg-warning
                            @endif"
                                        role="progressbar"
                                        style="width: {{ $clamped }}%;"
                                        aria-valuenow="{{ $clamped }}" aria-valuemin="0" aria-valuemax="100">
                                        {{ $clamped }}%
                                    </div>
                                </div>

                                <div class="mt-auto">
                                    <a href="{{ route('causes.show', $cause) }}" class="btn btn-outline-primary btn-sm">
                                        Donate now
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </section>
    {{-- Causes Section End --}}




    {{-- Donation Stats Start --}}
    <section class="py-5">
        <div class="container">

            {{-- KPIs --}}
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body text-center">
                            <div class="display-6 fw-bold">€{{ number_format($totalRaised, 2) }}</div>
                            <div class="text-muted">Total Raised</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body text-center">
                            <div class="display-6 fw-bold">{{ number_format($donationsCnt) }}</div>
                            <div class="text-muted">Total Donations</div>
                            <div class="small text-muted mt-1">Avg: €{{ number_format($avgDonation, 2) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="fw-semibold">Progress to All Goals</div>
                                <div class="badge bg-primary">{{ $globalPercent }}%</div>
                            </div>
                            <div class="progress" style="height: 14px;">
                                <div class="progress-bar @if($globalPercent>=100) bg-success @elseif($globalPercent>=50) bg-info @else bg-warning @endif"
                                     role="progressbar"
                                     style="width: {{ $globalPercent }}%;"
                                     aria-valuenow="{{ $globalPercent }}" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                            <div class="small text-muted mt-2">
                                Raised: €{{ number_format($totalRaised,2) }}
                                <span class="text-muted">/</span>
                                Goal: €{{ number_format($totalGoal,2) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Chart + Top Causes + Recent Donations --}}
            <div class="row g-4">
                {{-- Area: Chart --}}
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="m-0">Donations (Last 12 Months)</h5>
                                <span class="badge bg-light text-dark">Monthly €</span>
                            </div>
                            <canvas id="donationsChart" height="140"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Area: Top Causes --}}
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body">
                            <h5 class="mb-3">Top Causes</h5>
                            <ul class="list-group list-group-flush">
                                @forelse($topCauses as $i => $cause)
                                    @php
                                        $rank = $i + 1;
                                        $raised = (float) ($cause->donations_sum_amount ?? 0);
                                    @endphp
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <span class="badge rounded-pill bg-primary me-3" style="width:34px">{{ $rank }}</span>
                                            <a href="{{ route('causes.show', $cause) }}" class="text-decoration-none fw-semibold">{{ $cause->title }}</a>
                                        </div>
                                        <div class="fw-bold">€{{ number_format($raised, 2) }}</div>
                                    </li>
                                @empty
                                    <li class="list-group-item text-muted">No donations yet.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Area: Recent Donations --}}
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body">
                            <h5 class="mb-3">Recent Donations</h5>
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead>
                                    <tr>
                                        <th>Donor</th>
                                        <th>Cause</th>
                                        <th class="text-end">Amount</th>
                                        <th>Date</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($recentDonations as $donation)
                                        <tr>
                                            <td class="fw-semibold">{{ $donation->donor_name }}</td>
                                            <td>
                                                @if($donation->cause)
                                                    <a href="{{ route('causes.show', $donation->cause) }}" class="text-decoration-none">
                                                        {{ $donation->cause->title }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td class="text-end">€{{ number_format($donation->amount, 2) }}</td>
                                            <td>{{ optional($donation->date)->format('d/m/Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-muted text-center">No recent donations.</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div> {{-- row --}}
        </div>
    </section>
    {{-- Donation Stats End --}}


    @push('scripts')
        {{-- Chart.js CDN (lightweight, drop-in) --}}
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            (function () {
                const ctx = document.getElementById('donationsChart');
                if (!ctx) return;

                const labels = @json($labels);
                const data    = @json($series);

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [{
                            label: '€ Raised',
                            data,
                            tension: 0.35,
                            fill: true,
                            borderWidth: 2,
                            pointRadius: 3,
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false },
                            tooltip: { mode: 'index', intersect: false }
                        },
                        scales: {
                            y: { beginAtZero: true, ticks: { callback: v => '€' + v } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            })();
        </script>
    @endpush






    {{-- Workshops Section Start --}}
    <section class="py-5">
        <div class="container text-center py-5">
            <h2 class="display-5 fw-bold mb-3">Skill-Up With Workshops</h2>
            <p class="lead mb-5">Hands-on sessions led by practitioners. Learn, build, and collaborate.</p>

            <a href="{{ route('workshops.index') }}"
               class="btn btn-outline-primary btn-lg rounded-pill px-5 py-3 fw-bold shadow-sm">
                Browse Workshops
            </a>

            <div class="row justify-content-center mt-5 g-4">
                @foreach($featuredWorkshops as $workshop)
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                            {{-- IMAGE --}}
                            @if(!empty($workshop->image_path))
                                <img src="{{ asset('storage/'.$workshop->image_path) }}"
                                     alt="{{ $workshop->title }}"
                                     class="card-img-top"
                                     style="height: 180px; object-fit: cover;">
                            @endif

                            <div class="card-body d-flex flex-column text-center">
                                <h4 class="mb-2">
                                    <a href="{{ route('workshops.show', $workshop) }}" class="text-decoration-none">
                                        {{ $workshop->title }}
                                    </a>
                                </h4>

                                <p class="text-muted mb-3">
                                    {{ \Illuminate\Support\Str::limit($workshop->description ?? $workshop->summary ?? '', 90) }}
                                </p>

                                {{-- Materials count (many-to-many) --}}
                                @isset($workshop->materials_count)
                                    <p class="small mb-3">
                                        <strong>Materials:</strong> {{ $workshop->materials_count }}
                                    </p>
                                @endisset

                                <div class="mt-auto">
                                    <a href="{{ route('workshops.show', $workshop) }}"
                                       class="btn btn-outline-primary btn-sm">
                                        View details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    {{-- Workshops Section End --}}




    {{-- Workshops Stats Start --}}
    <section class="py-5">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h2 class="display-6 fw-bold mb-0">Workshops Stats</h2>
                <span class="text-muted small">Last 12 months</span>
            </div>

            {{-- KPI Cards --}}
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body text-center">
                            <div class="display-6 fw-bold">{{ number_format($totalWorkshops) }}</div>
                            <div class="text-muted">Total Workshops</div>
                            <div class="small text-muted mt-1">Published: {{ $publishedCount }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body text-center">
                            <div class="display-6 fw-bold">{{ number_format($upcomingCount) }}</div>
                            <div class="text-muted">Upcoming</div>
                            <div class="small text-muted mt-1">Past: {{ $pastCount }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body text-center">
                            <div class="display-6 fw-bold">{{ number_format($avgCapacity) }}</div>
                            <div class="text-muted">Avg Capacity</div>
                            <div class="small text-muted mt-1">Total: {{ number_format($totalCapacity) }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body text-center">
                            <div class="display-6 fw-bold">{{ number_format($avgMaterials) }}</div>
                            <div class="text-muted">Avg Materials / Workshop</div>
                            <div class="small text-muted mt-1">Based on linked items</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Chart + Top Lists --}}
            <div class="row g-4">
                {{-- Line Chart --}}
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="m-0">Workshops per Month</h5>
                                <span class="badge bg-light text-dark">Published</span>
                            </div>
                            <canvas id="workshopsChart" height="150"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Top Workshops (by materials count) --}}
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body">
                            <h5 class="mb-3">Top Workshops</h5>
                            <ul class="list-group list-group-flush">
                                @forelse($topWorkshops as $i => $w)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <span class="badge rounded-pill bg-primary me-3" style="width:34px">{{ $i+1 }}</span>
                                            <a href="{{ route('workshops.show', $w) }}" class="text-decoration-none fw-semibold">
                                                {{ $w->title }}
                                            </a>
                                        </div>
                                        <div class="fw-bold">{{ $w->materials_count ?? 0 }} items</div>
                                    </li>
                                @empty
                                    <li class="list-group-item text-muted">No workshops found.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Top Venues (by upcoming count) --}}
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body">
                            <h5 class="mb-3">Top Venues (Upcoming)</h5>
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Venue</th>
                                        <th class="text-end">Upcoming Workshops</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($topVenues as $i => $venue)
                                        <tr>
                                            <td>{{ $i+1 }}</td>
                                            <td>{{ $venue->name }}</td>
                                            <td class="text-end">{{ $venue->upcoming_count }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-center text-muted">No venues found.</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>{{-- row --}}
        </div>
    </section>
    {{-- Workshops Stats End --}}

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            (function () {
                const el = document.getElementById('workshopsChart');
                if (!el) return;

                const labels = @json($labels);
                const ws     = @json($seriesWorkshops);

                new Chart(el, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Workshops',
                            data: ws,
                            borderWidth: 2,
                            tension: 0.35,
                            pointRadius: 3,
                            fill: false
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: true },
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







    {{-- Events Section Start --}}
    <section class="py-5 bg-light">
        <div class="container text-center py-5">
            <h2 class="display-5 fw-bold mb-3">Upcoming Events</h2>
            <p class="lead mb-5">Meet, engage, and take action together at our community events.</p>

            <a href="{{ route('events.index') }}"
               class="btn btn-primary btn-lg rounded-pill px-5 py-3 fw-bold shadow-sm">
                See All Events
            </a>

            <div class="row justify-content-center mt-5 g-4">
                @foreach($featuredEvents as $event)
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                            {{-- IMAGE (optional if you added image_path to events) --}}
                            @if(!empty($event->image_path))
                                <img src="{{ asset('storage/'.$event->image_path) }}"
                                     alt="{{ $event->title }}"
                                     class="card-img-top"
                                     style="height: 180px; object-fit: cover;">
                            @endif

                            <div class="card-body d-flex flex-column text-center">
                                <h4 class="mb-2">
                                    <a href="{{ route('events.show', $event) }}" class="text-decoration-none">
                                        {{ $event->title }}
                                    </a>
                                </h4>

                                {{-- Date & venue/lieu --}}
                                <p class="small text-muted mb-2">
                                    @if(!empty($event->start_at))
                                        <i class="bi bi-calendar-event"></i>
                                        {{ \Carbon\Carbon::parse($event->start_at)->format('d/m/Y H:i') }}
                                    @endif
                                    @if($event->lieu?->name)
                                        <span class="text-muted">&middot;</span>
                                        <i class="bi bi-geo-alt"></i> {{ $event->lieu->name }}
                                    @endif
                                </p>

                                <p class="text-muted mb-3">
                                    {{ \Illuminate\Support\Str::limit($event->description ?? '', 90) }}
                                </p>

                                <div class="mt-auto">
                                    <a href="{{ route('events.show', $event) }}"
                                       class="btn btn-outline-primary btn-sm">
                                        View details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    {{-- Events Section End --}}





    {{-- Auth Buttons Section --}}
    <section class="py-5 bg-dark text-center text-white">
        <div class="container">
            @guest
                <h2 class="fw-bold mb-4">Join Our Community</h2>
                <p class="lead mb-4">Create an account or log in to support causes, donate, and participate in events.</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-4">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-4">Register</a>
                </div>
            @endguest

            @auth
                <h2 class="fw-bold mb-3">Welcome back, {{ auth()->user()->name }}!</h2>
                <p class="lead mb-4">Head to your dashboard or explore new causes, workshops, and events.</p>
                <div class="d-flex justify-content-center gap-3">
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.causes.index') }}" class="btn btn-warning btn-lg px-4">Go to Admin</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-lg px-4">Logout</button>
                    </form>
                </div>
            @endauth
        </div>
    </section>


@endsection
