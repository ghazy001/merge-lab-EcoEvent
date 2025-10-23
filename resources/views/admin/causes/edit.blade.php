{{-- resources/views/admin/causes/edit.blade.php --}}
@extends('layouts.admin')
@section('title','Edit Cause')
@section('content')
    <div class="container">
        <h1>Edit Cause</h1>
        <form action="{{ route('admin.causes.update', $cause) }}" method="POST" enctype="multipart/form-data">
            @method('PUT')
            @include('admin.causes._form', ['buttonText' => 'Update Cause'])
        </form>
    </div>
@endsection
