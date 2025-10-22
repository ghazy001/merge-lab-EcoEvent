<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Flexy Admin') }}</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logos/favicon.png') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/styles.min.css') }}" />
    <script src="https://unpkg.com/alpinejs" defer></script>

    @stack('styles')
</head>
<body>
<!-- Body Wrapper -->
<div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
     data-sidebar-position="fixed" data-header-position="fixed">
    <!-- App Topstrip -->
    <div class="app-topstrip bg-dark py-6 px-3 w-100 d-lg-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center justify-content-center gap-5 mb-2 mb-lg-0">
            <a class="d-flex justify-content-center" href="{{ url('/') }}">
                    <img src="{{ asset('assets/images/logos/logo-wrappixel.svg') }}" alt="" width="150">
            </a>
        </div>
        <div class="d-lg-flex align-items-center gap-2">

            <div class="d-flex align-items-center justify-content-center gap-2">
            </div>
        </div>
    </div>
    <!-- Sidebar -->
    @include('adminpartials.sidebar')
    <!-- Main wrapper -->
    <div class="body-wrapper">
        <!-- Header -->
        @include('adminpartials.header')
        <!-- Content -->
        <div class="body-wrapper-inner">
            <div class="container-fluid">
                @yield('content')
                <div class="py-6 px-6 text-center">
                    <p class="mb-0 fs-4">
                        Design and Developed by
                        <a href="https://github.com/ghazy001" class="pe-1 text-primary text-decoration-underline">Ghazi saoudi</a>
                        Distributed by
                        <a href="https://github.com/ghazy001" class="pe-1 text-primary text-decoration-underline">Ghazi saoudi</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('assets/libs/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/sidebarmenu.js') }}"></script>
<script src="{{ asset('assets/js/app.min.js') }}"></script>
<script src="{{ asset('assets/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/libs/simplebar/dist/simplebar.js') }}"></script>
<script src="{{ asset('assets/js/dashboard.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
@stack('scripts')
</body>
</html>
