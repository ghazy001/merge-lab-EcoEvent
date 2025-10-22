<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>@yield('title','EcoEvent - Environmental & Nature Website Template')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Google Web Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500;600&family=Roboto&display=swap" rel="stylesheet">

    {{-- Icon Fonts --}}
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Libraries Stylesheet --}}
    <link href="{{ asset('lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">
    <link href="{{ asset('lib/lightbox/css/lightbox.min.css') }}" rel="stylesheet">

    {{-- Bootstrap & Template Stylesheet --}}
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

    <meta name="csrf-token" content="{{ csrf_token() }}">


    @stack('styles')
</head>


<body>

{{-- Spinner --}}
<div id="spinner" class="show w-100 vh-100 bg-white position-fixed translate-middle top-50 start-50 d-flex align-items-center justify-content-center">
    <div class="spinner-grow text-primary" role="status"></div>
</div>

{{-- Header/Navbar --}}
@include('partials.header')

{{-- Page content --}}
<main class="pt-5 mt-5">
    @yield('content')
</main>

{{-- Footer --}}
@include('partials.footer')

{{-- Back to Top --}}
<a href="#" class="btn btn-primary btn-primary-outline-0 btn-md-square back-to-top">
    <i class="fa fa-arrow-up"></i>
</a>

{{-- JavaScript Libraries --}}
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('lib/easing/easing.min.js') }}"></script>
<script src="{{ asset('lib/waypoints/waypoints.min.js') }}"></script>
<script src="{{ asset('lib/counterup/counterup.min.js') }}"></script>
<script src="{{ asset('lib/owlcarousel/owl.carousel.min.js') }}"></script>
<script src="{{ asset('lib/lightbox/js/lightbox.min.js') }}"></script>

{{-- Template JS --}}
<script src="{{ asset('js/main.js') }}"></script>

@stack('scripts')
<x-chatbot />
@stack('scripts')

</body>
</html>
