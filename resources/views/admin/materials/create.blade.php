{{-- resources/views/admin/materials/create.blade.php --}}
@extends('layouts.admin')
@section('content')
    <div class="container">
        <h1>Nouveau Matériel</h1>
        @if($errors->any())
            <div class="alert alert-danger"><ul class="mb-0">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul></div>
        @endif
        <form method="POST" action="{{ route('admin.materials.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nom</label>
                    <input name="name" class="form-control" required value="{{ old('name') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Stock</label>
                    <input type="number" name="stock" class="form-control" min="0" value="{{ old('stock',0) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Unité</label>
                    <input name="unit" class="form-control" value="{{ old('unit') }}" placeholder="pcs, kg...">
                </div>
            </div>
            <div class="mt-3">
                <button class="btn btn-primary">Enregistrer</button>
                <a href="{{ route('admin.materials.index') }}" class="btn btn-light">Annuler</a>
            </div>
        </form>
    </div>
@endsection
