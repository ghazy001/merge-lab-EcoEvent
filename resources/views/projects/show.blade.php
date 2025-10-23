@extends('layouts.app')
@section('title', $project->title)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/project-show.css') }}">
@endpush

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-11">
                <!-- Project Header Card -->
                <div class="project-header-card mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="display-5 fw-bold mb-3">{{ $project->title }}</h1>

                            @if($project->description)
                                <p class="lead text-secondary mb-3">{{ $project->description }}</p>
                            @endif

                            <div class="d-flex gap-3 flex-wrap">
                                <span class="badge-custom status-{{ strtolower($project->status) }}">
                                    <i class="bi bi-flag-fill"></i> {{ $project->status }}
                                </span>
                            </div>
                        </div>

                        <div class="col-md-4 mt-4 mt-md-0">
                            <!-- Progress Circle -->
                            <div class="text-center">
                                <div class="progress-circle mx-auto" style="--progress: {{ $project->progress }}%;">
                                    <div class="progress-circle-inner">
                                        <span class="progress-value">{{ $project->progress }}%</span>
                                        <span class="progress-label">Complete</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tasks Section -->
                <div class="tasks-section">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="mb-0">
                            <i class="bi bi-list-check text-primary"></i> Tasks
                            <span class="badge bg-light text-dark ms-2">{{ $tasks->count() }}</span>
                        </h3>
                    </div>

                    @forelse($tasks as $t)
                        <div class="task-card mb-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <h5 class="mb-0 task-title">{{ $t->title }}</h5>
                                        <span class="badge task-status-{{ strtolower($t->status) }}">
                                            {{ $t->status }}
                                        </span>
                                    </div>

                                    @if($t->description)
                                        <p class="text-muted mb-2">{{ Str::limit($t->description, 150) }}</p>
                                    @endif

                                    <div class="task-meta">
                                        @if($t->due_date)
                                            <span class="meta-item {{ $t->due_date->isPast() && $t->status !== 'completed' ? 'text-danger' : '' }}">
                                                <i class="bi bi-calendar-event"></i>
                                                {{ $t->due_date->format('M d, Y') }}
                                            </span>
                                        @endif
                                        @if($t->assigned_to)
                                            <span class="meta-item">
                                                <i class="bi bi-person"></i>
                                                {{ $t->assignedUser->name ?? 'Assigned' }}
                                            </span>
                                        @endif
                                        @if($t->priority)
                                            <span class="meta-item priority-{{ strtolower($t->priority) }}">
                                                <i class="bi bi-exclamation-circle"></i>
                                                {{ $t->priority }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <i class="bi bi-inbox display-1 text-muted mb-3"></i>
                            <h4 class="text-muted">No tasks yet</h4>
                        </div>
                    @endforelse
                </div>

                <!-- Back Button -->
                <div class="mt-4">
                    <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Projects
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
