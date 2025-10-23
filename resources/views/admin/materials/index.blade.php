{{-- resources/views/admin/materials/index.blade.php --}}
@extends('layouts.admin')
@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Matériels</h1>
            <a href="{{ route('admin.materials.create') }}" class="btn btn-primary btn-sm">Nouveau</a>
        </div>
        @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

        <table class="table table-sm">
            <thead><tr><th>#</th><th>Nom</th><th>Stock</th><th>Unité</th><th></th></tr></thead>
            <tbody>
            @foreach($materials as $m)
                <tr>
                    <td>{{ $m->id }}</td>
                    <td>{{ $m->name }}</td>
                    <td>{{ $m->stock }}</td>
                    <td>{{ $m->unit }}</td>
                    <td class="text-end">
                        <a href="{{ route('admin.materials.edit',$m) }}" class="btn btn-sm btn-outline-secondary">Éditer</a>
                        <form action="{{ route('admin.materials.destroy',$m) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Supprimer ce matériel ?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{ $materials->links() }}
    </div>
@endsection
