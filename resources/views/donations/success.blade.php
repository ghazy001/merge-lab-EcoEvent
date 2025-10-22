@extends('layouts.app')

@section('title','Thank you!')

@section('content')
    <div class="container py-5 text-center">
        <h1 class="mb-3">Thank you for your donation! 💖</h1>
        <p>Your payment was successful. We’ve recorded your donation.</p>

        <a href="{{ route('causes.index') }}" class="btn btn-primary mt-3">
            Back to causes
        </a>
    </div>
@endsection
