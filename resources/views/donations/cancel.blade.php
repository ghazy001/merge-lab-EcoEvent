@extends('layouts.app')

@section('title','Donation canceled')

@section('content')
    <div class="container py-5 text-center">
        <h1 class="mb-3 text-danger">Donation canceled ❌</h1>
        <p>Your payment was not completed. No money was charged.</p>

        <a href="{{ route('causes.index') }}" class="btn btn-outline-secondary mt-3">
            Back to causes
        </a>
    </div>
@endsection
