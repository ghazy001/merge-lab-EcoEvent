@extends('layouts.admin')
@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Catégories</h1>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">Nouvelle catégorie</a>
        </div>

        @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

        <div class="table-responsive card shadow-sm">
            <table class="table table-hover mb-0">
                <thead><tr><th>Nom</th><th>Slug</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                @forelse($categories as $c)
                    <tr>
                        <td>{{ $c->name }}</td>
                        <td class="text-muted">{{ $c->slug }}</td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.categories.edit',$c) }}">Éditer</a>
                            <form class="d-inline" method="POST" action="{{ route('admin.categories.destroy',$c) }}">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer ?')">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center text-muted">Aucune catégorie.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">{{ $categories->links() }}</div>
    </div>
@endsection
