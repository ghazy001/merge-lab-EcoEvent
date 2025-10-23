@extends('layouts.app')
@section('title', $event->title)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/event-show.css') }}">
@endpush

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Event Header -->
                <div class="event-header-card mb-4">
                    <div class="event-date-badge">
                        <div class="date-day">{{ \Carbon\Carbon::parse($event->start_at)->format('d') }}</div>
                        <div class="date-month">{{ \Carbon\Carbon::parse($event->start_at)->format('M') }}</div>
                    </div>

                    <div class="event-header-content">
                        <h1 class="display-4 fw-bold mb-3">{{ $event->title }}</h1>

                        <div class="event-meta-primary">
                            <div class="meta-item-large">
                                <i class="bi bi-calendar-event"></i>
                                <div>
                                    <div class="meta-label">Date de début</div>
                                    <div class="meta-value">{{ \Carbon\Carbon::parse($event->start_at)->format('d/m/Y à H:i') }}</div>
                                </div>
                            </div>

                            @if($event->end_at)
                                <div class="meta-item-large">
                                    <i class="bi bi-calendar-check"></i>
                                    <div>
                                        <div class="meta-label">Date de fin</div>
                                        <div class="meta-value">{{ \Carbon\Carbon::parse($event->end_at)->format('d/m/Y à H:i') }}</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Event Details -->
                <div class="row g-4">
                    <!-- Main Content -->
                    <div class="col-lg-8">
                        <div class="event-card">
                            <h3 class="card-title">
                                <i class="bi bi-info-circle text-primary"></i> À propos de l'événement
                            </h3>

                            @if($event->description)
                                <div class="event-description">
                                    {!! nl2br(e($event->description)) !!}
                                </div>
                            @else
                                <p class="text-muted">Aucune description disponible.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="col-lg-4">
                        <!-- Location Card -->
                        @if($event->lieu)
                            <div class="event-card info-card">
                                <h5 class="info-card-title">
                                    <i class="bi bi-geo-alt-fill text-danger"></i> Lieu
                                </h5>
                                <div class="info-card-content">
                                    <div class="location-name">{{ $event->lieu->name }}</div>
                                    <div class="location-address">
                                        <i class="bi bi-pin-map"></i>
                                        {{ $event->lieu->address }}
                                    </div>
                                    @if($event->lieu->city)
                                        <div class="location-city text-muted mt-1">
                                            {{ $event->lieu->city }}
                                            @if($event->lieu->postal_code), {{ $event->lieu->postal_code }}@endif
                                        </div>
                                    @endif

                                    <!-- Map -->
                                    <div class="location-map mt-3">
                                        @php
                                            $fullAddress = $event->lieu->address;
                                            if($event->lieu->city) {
                                                $fullAddress .= ', ' . $event->lieu->city;
                                            }
                                            if($event->lieu->postal_code) {
                                                $fullAddress .= ' ' . $event->lieu->postal_code;
                                            }
                                        @endphp

                                        <iframe
                                            width="100%"
                                            height="250"
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
                                </div>
                            </div>
                        @endif

                        <!-- Capacity Card -->
                        @if($event->capacity)
                            <div class="event-card info-card">
                                <h5 class="info-card-title">
                                    <i class="bi bi-people-fill text-success"></i> Capacité
                                </h5>
                                <div class="info-card-content">
                                    <div class="capacity-display">
                                        <span class="capacity-number">{{ $event->capacity }}</span>
                                        <span class="capacity-label">places</span>
                                    </div>
                                    @if(isset($event->registered_count))
                                        <div class="capacity-bar mt-3">
                                            <div class="capacity-bar-fill" style="width: {{ ($event->registered_count / $event->capacity) * 100 }}%;"></div>
                                        </div>
                                        <div class="capacity-text mt-2">
                                            <small class="text-muted">
                                                {{ $event->registered_count }} / {{ $event->capacity }} inscrits
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Quick Info Card -->
                        <div class="event-card info-card">
                            <h5 class="info-card-title">
                                <i class="bi bi-clock-fill text-primary"></i> Informations rapides
                            </h5>
                            <div class="info-card-content">
                                <div class="quick-info-item">
                                    <span class="quick-info-label">Durée:</span>
                                    <span class="quick-info-value">
                                        @if($event->end_at)
                                            {{ \Carbon\Carbon::parse($event->start_at)->diffInHours(\Carbon\Carbon::parse($event->end_at)) }}h
                                        @else
                                            Non spécifiée
                                        @endif
                                    </span>
                                </div>
                                <div class="quick-info-item">
                                    <span class="quick-info-label">Statut:</span>
                                    <span class="badge bg-{{ \Carbon\Carbon::parse($event->start_at)->isFuture() ? 'success' : 'secondary' }}">
                                        {{ \Carbon\Carbon::parse($event->start_at)->isFuture() ? 'À venir' : 'Terminé' }}
                                    </span>
                                </div>
                                @if($event->category)
                                    <div class="quick-info-item">
                                        <span class="quick-info-label">Catégorie:</span>
                                        <span class="badge bg-primary">{{ $event->category->name }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Back Button -->
                <div class="mt-5">
                    <a href="{{ route('events.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Retour aux événements
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
