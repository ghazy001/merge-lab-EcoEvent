@extends('layouts.app')
@section('title', $cause->title)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/cause-show.css') }}">
@endpush

@section('content')
    <div class="container py-5">
        @include('partials.flash')

        <div class="row justify-content-center">
            <div class="col-lg-11">
                <!-- Cause Header -->
                <div class="cause-header mb-5">
                    @if($cause->image_path)
                        <div class="cause-hero-image position-relative">
                            <img src="{{ asset('storage/'.$cause->image_path) }}"
                                 alt="{{ $cause->title }}"
                                 class="w-100">

                            <!-- Hero Badge reflects status -->
                            <div class="hero-overlay">
                                <div class="hero-content">
                                    <span class="hero-badge">
                                        @if($cause->status === 'active')
                                            <i class="bi bi-heart-fill"></i> Cause Active
                                        @elseif($cause->status === 'completed')
                                            <i class="bi bi-patch-check-fill"></i> Completed
                                        @elseif($cause->status === 'canceled')
                                            <i class="bi bi-slash-circle-fill"></i> Canceled
                                        @else
                                            <i class="bi bi-info-circle-fill"></i> Status
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="cause-header-content">
                        <h1 class="display-4 fw-bold mb-3">{{ $cause->title }}</h1>
                        <p class="lead text-secondary">{{ $cause->description }}</p>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Main Content -->
                    <div class="col-lg-8">
                        <!-- Progress Section -->
                        <div class="progress-card">
                            <div class="row align-items-center mb-4">
                                <div class="col-md-6">
                                    <div class="amount-raised">
                                        <div class="amount-label">Raised</div>
                                        <div class="amount-value">€{{ number_format($cause->totalDonations(), 0) }}</div>
                                        <div class="amount-goal">of €{{ number_format($cause->goal_amount, 0) }} goal</div>
                                    </div>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <div class="progress-stats">
                                        <div class="stat-item">
                                            <div class="stat-number">{{ $cause->percentRaised() }}%</div>
                                            <div class="stat-label">Funded</div>
                                        </div>
                                        <div class="stat-item">
                                            <div class="stat-number">{{ $cause->donations->count() }}</div>
                                            <div class="stat-label">Backers</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div class="progress-bar-container">
                                <div class="progress-bar-fill" style="width: {{ min($cause->percentRaised(), 100) }}%;"></div>
                            </div>

                            @if($cause->percentRaised() >= 100)
                                <div class="goal-reached-badge mt-3">
                                    <i class="bi bi-check-circle-fill"></i> Goal Reached! Thank you to all supporters!
                                </div>
                            @else
                                <div class="remaining-amount mt-3">
                                    <i class="bi bi-info-circle"></i>
                                    €{{ number_format(max(0, $cause->goal_amount - $cause->totalDonations()), 2) }} still needed to reach the goal
                                </div>
                            @endif
                        </div>

                        <!-- Donations List -->
                        <div class="donations-section">
                            <h3 class="section-title">
                                <i class="bi bi-people-fill text-primary"></i>
                                Recent Supporters
                                <span class="badge bg-light text-dark ms-2">{{ $cause->donations->count() }}</span>
                            </h3>

                            <div class="donations-list">
                                @forelse($cause->donations->sortByDesc('date') as $donation)
                                    <div class="donation-item">
                                        <div class="donation-avatar">
                                            <div class="avatar-circle">
                                                {{ strtoupper(substr($donation->donor_name, 0, 1)) }}
                                            </div>
                                        </div>
                                        <div class="donation-info">
                                            <div class="donor-name">{{ $donation->donor_name }}</div>
                                            <div class="donation-date">
                                                <i class="bi bi-calendar3"></i>
                                                {{ $donation->date->format('F d, Y') }}
                                            </div>
                                            @if($donation->message)
                                                <div class="donation-message">
                                                    <i class="bi bi-chat-left-quote"></i>
                                                    "{{ $donation->message }}"
                                                </div>
                                            @endif
                                        </div>
                                        <div class="donation-amount">
                                            €{{ number_format($donation->amount, 2) }}
                                        </div>
                                    </div>
                                @empty
                                    <div class="empty-donations">
                                        <i class="bi bi-gift display-1 text-muted mb-3"></i>
                                        <h5 class="text-muted">Be the first to support this cause!</h5>
                                        <p class="text-secondary">Your donation can make a real difference</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Donation Sidebar -->
                    <div class="col-lg-4">
                        <div class="donation-card sticky-top">
                            <div class="donation-card-header">
                                <h4 class="mb-0">
                                    <i class="bi bi-heart-fill text-danger"></i>
                                    Make a Donation
                                </h4>
                                <p class="text-muted small mb-0 mt-2">Every contribution helps us reach our goal</p>
                            </div>

                            @if($cause->status === 'active')
                                <!-- Donation Form (active only) -->
                                <form method="POST" action="{{ route('causes.donations.checkout', $cause) }}" class="donation-form">
                                @csrf

                                    @auth
                                        <input type="hidden" name="donor_name" value="{{ auth()->user()->name }}">
                                        <div class="donor-info">
                                            <i class="bi bi-person-circle"></i>
                                            Donating as <strong>{{ auth()->user()->name }}</strong>
                                        </div>
                                    @else
                                        <div class="form-group">
                                            <label class="form-label">Your Name</label>
                                            <div class="input-icon">
                                                <i class="bi bi-person"></i>
                                                <input name="donor_name"
                                                       class="form-control @error('donor_name') is-invalid @enderror"
                                                       placeholder="Enter your name"
                                                       value="{{ old('donor_name') }}"
                                                       required>
                                            </div>
                                            @error('donor_name')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @endauth

                                    <div class="form-group">
                                        <label class="form-label">Donation Amount</label>
                                        <div class="input-icon">
                                            <i class="bi bi-currency-euro"></i>
                                            <input name="amount"
                                                   type="number"
                                                   step="0.01"
                                                   min="1"
                                                   class="form-control @error('amount') is-invalid @enderror"
                                                   placeholder="Enter amount"
                                                   value="{{ old('amount') }}"
                                                   required>
                                        </div>
                                        @error('amount')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Quick Amount Buttons -->
                                    <div class="quick-amounts">
                                        <button type="button" class="quick-amount-btn" onclick="setAmount(10)">€10</button>
                                        <button type="button" class="quick-amount-btn" onclick="setAmount(25)">€25</button>
                                        <button type="button" class="quick-amount-btn" onclick="setAmount(50)">€50</button>
                                        <button type="button" class="quick-amount-btn" onclick="setAmount(100)">€100</button>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Message (Optional)</label>
                                        <textarea name="message"
                                                  class="form-control"
                                                  rows="3"
                                                  placeholder="Share why you're supporting this cause...">{{ old('message') }}</textarea>
                                    </div>

                                    <button type="submit" class="btn btn-donate">
                                        <i class="bi bi-heart-fill"></i>
                                        Donate Now
                                    </button>

                                    <div class="donation-secure">
                                        <i class="bi bi-shield-check"></i>
                                        Secure & encrypted transaction
                                    </div>
                                </form>
                            @else
                                <!-- Locked / closed donations -->
                                <div class="p-4 text-center">
                                    <div class="mb-2" style="font-size:2rem;">
                                        @if($cause->status === 'completed')
                                            <i class="bi bi-patch-check-fill text-success" title="Completed"></i>
                                        @elseif($cause->status === 'canceled')
                                            <i class="bi bi-slash-circle-fill text-muted" title="Canceled"></i>
                                        @else
                                            <i class="bi bi-info-circle-fill text-muted"></i>
                                        @endif
                                    </div>
                                    <h5 class="mb-1">
                                        @if($cause->status === 'completed')
                                            This cause is completed 🎉
                                        @elseif($cause->status === 'canceled')
                                            This cause has been canceled
                                        @else
                                            Donations are closed
                                        @endif
                                    </h5>
                                    <p class="text-secondary mb-0">Donations are no longer accepted.</p>
                                </div>
                            @endif

                            <!-- Share Section -->
                            <div class="share-section">
                                <h6 class="share-title">Share this cause</h6>
                                <div class="share-buttons">
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                                       target="_blank"
                                       class="share-btn share-facebook">
                                        <i class="bi bi-facebook"></i>
                                    </a>
                                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($cause->title) }}"
                                       target="_blank"
                                       class="share-btn share-twitter">
                                        <i class="bi bi-twitter"></i>
                                    </a>
                                    <a href="https://wa.me/?text={{ urlencode($cause->title . ' ' . url()->current()) }}"
                                       target="_blank"
                                       class="share-btn share-whatsapp">
                                        <i class="bi bi-whatsapp"></i>
                                    </a>
                                    <a href="mailto:?subject={{ urlencode($cause->title) }}&body={{ urlencode(url()->current()) }}"
                                       class="share-btn share-email">
                                        <i class="bi bi-envelope"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Back Button -->
                <div class="mt-5">
                    <a href="{{ route('causes.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to All Causes
                    </a>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function setAmount(amount) {
                const input = document.querySelector('input[name="amount"]');
                if (input) input.value = amount;
            }
        </script>
    @endpush
@endsection
