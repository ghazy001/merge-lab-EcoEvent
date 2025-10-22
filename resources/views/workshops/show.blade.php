@extends('layouts.app')
@section('title', $workshop->title)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/workshop-show.css') }}">
@endpush

@section('content')
    <div class="container py-5">
        @include('partials.flash')

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Workshop Header -->
                <div class="workshop-header-card">
                    <div class="header-icon">
                        <i class="bi bi-tools"></i>
                    </div>
                    <div class="header-content">
                        <h1 class="display-4 fw-bold mb-3">{{ $workshop->title }}</h1>

                        <div class="workshop-meta">
                            @if($workshop->lieu)
                                <div class="meta-item">
                                    <i class="bi bi-geo-alt-fill"></i>
                                    <span>{{ $workshop->lieu->name }}</span>
                                </div>
                            @endif

                            @if($workshop->start_date)
                                <div class="meta-item">
                                    <i class="bi bi-calendar-event"></i>
                                    <span>{{ \Carbon\Carbon::parse($workshop->start_date)->format('d/m/Y') }}</span>
                                </div>
                            @endif

                            @if($workshop->duration)
                                <div class="meta-item">
                                    <i class="bi bi-clock"></i>
                                    <span>{{ $workshop->duration }} heures</span>
                                </div>
                            @endif

                            @if($workshop->capacity)
                                <div class="meta-item">
                                    <i class="bi bi-people"></i>
                                    <span>{{ $workshop->capacity }} places</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Main Content -->
                    <div class="col-lg-8">
                        <!-- Description Card -->
                        <div class="content-card">
                            <h3 class="card-title">
                                <i class="bi bi-info-circle text-primary"></i>
                                Description de l'atelier
                            </h3>
                            <div class="workshop-description">
                                @if($workshop->description)
                                    {!! nl2br(e($workshop->description)) !!}
                                @else
                                    <p class="text-muted">Aucune description disponible.</p>
                                @endif
                            </div>
                        </div>

                        <!-- Materials Card -->
                        <div class="content-card">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h3 class="card-title mb-0">
                                    <i class="bi bi-box-seam text-warning"></i>
                                    Matériels requis
                                    <span class="badge bg-light text-dark ms-2">{{ $workshop->materials->count() }}</span>
                                </h3>
                            </div>

                            @forelse($workshop->materials as $material)
                                <div class="material-item">
                                    <div class="material-icon">
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                    </div>
                                    <div class="material-info">
                                        <div class="material-name">{{ $material->name }}</div>
                                        @if($material->description)
                                            <div class="material-description">{{ $material->description }}</div>
                                        @endif
                                    </div>
                                    <div class="material-quantity">
                                    <span class="quantity-badge">
                                        <strong>{{ $material->pivot->quantity }}</strong>
                                        @if($material->unit)
                                            {{ $material->unit }}
                                        @endif
                                    </span>
                                    </div>
                                </div>
                            @empty
                                <div class="empty-materials">
                                    <i class="bi bi-inbox display-1 text-muted mb-3"></i>
                                    <h5 class="text-muted">Aucun matériel requis</h5>
                                    <p class="text-secondary">Cet atelier ne nécessite pas de matériel spécifique</p>
                                </div>
                            @endforelse
                        </div>

                        <!-- Additional Info Card -->
                        @if($workshop->objectives || $workshop->prerequisites)
                            <div class="content-card">
                                @if($workshop->objectives)
                                    <div class="info-section">
                                        <h5 class="info-section-title">
                                            <i class="bi bi-bullseye text-success"></i>
                                            Objectifs
                                        </h5>
                                        <div class="info-content">
                                            {!! nl2br(e($workshop->objectives)) !!}
                                        </div>
                                    </div>
                                @endif

                                @if($workshop->prerequisites)
                                    <div class="info-section">
                                        <h5 class="info-section-title">
                                            <i class="bi bi-clipboard-check text-info"></i>
                                            Prérequis
                                        </h5>
                                        <div class="info-content">
                                            {!! nl2br(e($workshop->prerequisites)) !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Sidebar -->
                    <div class="col-lg-4">
                        <!-- Quick Info Card -->
                        <div class="sidebar-card">
                            <h5 class="sidebar-card-title">
                                <i class="bi bi-info-circle-fill"></i>
                                Informations
                            </h5>
                            <div class="quick-info-list">
                                @if($workshop->instructor)
                                    <div class="quick-info-item">
                                        <div class="info-label">
                                            <i class="bi bi-person-badge"></i>
                                            Instructeur
                                        </div>
                                        <div class="info-value">{{ $workshop->instructor }}</div>
                                    </div>
                                @endif

                                @if($workshop->price)
                                    <div class="quick-info-item">
                                        <div class="info-label">
                                            <i class="bi bi-currency-euro"></i>
                                            Prix
                                        </div>
                                        <div class="info-value price">€{{ number_format($workshop->price, 2) }}</div>
                                    </div>
                                @endif

                                @if($workshop->level)
                                    <div class="quick-info-item">
                                        <div class="info-label">
                                            <i class="bi bi-bar-chart"></i>
                                            Niveau
                                        </div>
                                        <div class="info-value">
                                        <span class="badge level-{{ strtolower($workshop->level) }}">
                                            {{ $workshop->level }}
                                        </span>
                                        </div>
                                    </div>
                                @endif

                                @if($workshop->status)
                                    <div class="quick-info-item">
                                        <div class="info-label">
                                            <i class="bi bi-flag"></i>
                                            Statut
                                        </div>
                                        <div class="info-value">
                                        <span class="badge status-{{ strtolower($workshop->status) }}">
                                            {{ $workshop->status }}
                                        </span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Location Card -->
                        @if($workshop->lieu)
                            <div class="sidebar-card location-card">
                                <h5 class="sidebar-card-title">
                                    <i class="bi bi-geo-alt-fill text-danger"></i>
                                    Lieu
                                </h5>
                                <div class="location-info">
                                    <div class="location-name">{{ $workshop->lieu->name }}</div>
                                    @if($workshop->lieu->address)
                                        <div class="location-address">
                                            <i class="bi bi-pin-map"></i>
                                            {{ $workshop->lieu->address }}
                                        </div>
                                    @endif
                                    @if($workshop->lieu->city)
                                        <div class="location-city">
                                            {{ $workshop->lieu->city }}
                                            @if($workshop->lieu->postal_code), {{ $workshop->lieu->postal_code }}@endif
                                        </div>
                                    @endif

                                    @if($workshop->lieu->address)
                                        <div class="location-map mt-3">
                                            @php
                                                $fullAddress = $workshop->lieu->address;
                                                if($workshop->lieu->city) {
                                                    $fullAddress .= ', ' . $workshop->lieu->city;
                                                }
                                                if($workshop->lieu->postal_code) {
                                                    $fullAddress .= ' ' . $workshop->lieu->postal_code;
                                                }
                                            @endphp

                                            <iframe
                                                width="100%"
                                                height="200"
                                                style="border:0; border-radius: 8px;"
                                                loading="lazy"
                                                allowfullscreen
                                                referrerpolicy="no-referrer-when-downgrade"
                                                src="https://maps.google.com/maps?q={{ urlencode($fullAddress) }}&t=&z=15&ie=UTF8&iwloc=&output=embed">
                                            </iframe>

                                            <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($fullAddress) }}"
                                               target="_blank"
                                               class="btn btn-sm btn-outline-primary w-100 mt-2">
                                                <i class="bi bi-map"></i> Ouvrir dans Google Maps
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Registration Button -->
                        @if($workshop->status === 'open' || $workshop->status === 'upcoming')
                            <div class="d-grid gap-2">
                                <a href="{{ route('workshops.register', $workshop) }}" class="btn btn-register">
                                    <i class="bi bi-person-plus-fill"></i>
                                    S'inscrire à l'atelier
                                </a>
                            </div>
                        @endif

                        <!-- Share Card -->
                        <div class="sidebar-card share-card">
                            <h6 class="share-title">Partager cet atelier</h6>
                            <div class="share-buttons">
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                                   target="_blank"
                                   class="share-btn share-facebook">
                                    <i class="bi bi-facebook"></i>
                                </a>
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($workshop->title) }}"
                                   target="_blank"
                                   class="share-btn share-twitter">
                                    <i class="bi bi-twitter"></i>
                                </a>
                                <a href="https://wa.me/?text={{ urlencode($workshop->title . ' ' . url()->current()) }}"
                                   target="_blank"
                                   class="share-btn share-whatsapp">
                                    <i class="bi bi-whatsapp"></i>
                                </a>
                                <a href="mailto:?subject={{ urlencode($workshop->title) }}&body={{ urlencode(url()->current()) }}"
                                   class="share-btn share-email">
                                    <i class="bi bi-envelope"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Back Button -->
                <div class="mt-5">
                    <a href="{{ route('workshops.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Retour aux ateliers
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
