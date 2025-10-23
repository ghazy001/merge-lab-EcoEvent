<header class="app-header">
    <nav class="navbar navbar-expand-lg navbar-light">
        <ul class="navbar-nav">
            <!-- Sidebar toggle (mobile) -->
            <li class="nav-item d-block d-xl-none">
                <a class="nav-link sidebartoggler" id="headerCollapse" href="javascript:void(0)">
                    <i class="ti ti-menu-2"></i>
                </a>
            </li>

            <!-- Notifications -->
            <li class="nav-item dropdown">
                <a class="nav-link" href="javascript:void(0)" id="drop1" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ti ti-bell"></i>
                    <div class="notification bg-primary rounded-circle"></div>
                </a>
                <div class="dropdown-menu dropdown-menu-animate-up" aria-labelledby="drop1">
                    <div class="message-body">
                        <a href="javascript:void(0)" class="dropdown-item">Empty</a>
                    </div>
                </div>
            </li>
        </ul>

        <!-- User Menu -->
        <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
            <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
                <li class="nav-item dropdown">
                    <a class="nav-link d-flex align-items-center gap-2" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown" aria-expanded="false">
                        <!-- Avatar -->
                        <img src="{{ asset('assets/images/profile/user-1.jpg') }}"
                             alt="User" width="35" height="35" class="rounded-circle">
                        <!-- Username -->
                        <span class="fw-bold d-none d-md-inline">
                            {{ Auth::user()->name ?? 'Guest' }}
                        </span>
                    </a>

                    <!-- Dropdown -->
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
                        <div class="message-body">
                            <!-- Home link -->
                            <a href="{{ url('/') }}" class="d-flex align-items-center gap-2 dropdown-item">
                                <i class="ti ti-home fs-6"></i>
                                <p class="mb-0 fs-3">Home</p>
                            </a>

                            <!-- Logout -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="d-flex align-items-center gap-2 dropdown-item border-0 bg-transparent">
                                    <i class="ti ti-logout fs-6"></i>
                                    <p class="mb-0 fs-3">Logout</p>
                                </button>
                            </form>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</header>
