{{-- resources/views/admin/materials/edit.blade.php --}}
@extends('layouts.admin')
@section('content')
    <div class="container">
        <h1>Éditer Matériel</h1>
        @if($errors->any())
            <div class="alert alert-danger"><ul class="mb-0">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul></div>
        @endif
        <form method="POST" action="{{ route('admin.materials.update',$material) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nom</label>
                    <input name="name" class="form-control" required value="{{ old('name',$material->name) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Stock</label>
                    <input type="number" name="stock" class="form-control" min="0" value="{{ old('stock',$material->stock) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Unité</label>
                    <input name="unit" class="form-control" value="{{ old('unit',$material->unit) }}">
                </div>
            </div>
            <div class="mt-3">
                <button class="btn btn-primary">Mettre à jour</button>
                <a href="{{ route('admin.materials.index') }}" class="btn btn-light">Annuler</a>
            </div>
        </form>
    </div>
@endsection
