@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1 class="mb-4">Lieux</h1>

        <a href="{{ route('admin.lieux.create') }}" class="btn btn-primary mb-3">Nouveau lieu</a>

        <!-- Success message -->
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                <tr>
                    <th>Nom</th>
                    <th>Adresse</th>
                    <th>Capacité</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($lieux as $lieu)
                    <tr>
                        <td>{{ $lieu->name }}</td>
                        <td>{{ $lieu->address }}</td>
                        <td>{{ $lieu->capacity }}</td>
                        <td>
                            <a href="{{ route('admin.lieux.edit', $lieu) }}" class="btn btn-sm btn-secondary">Modifier</a>

                            <form action="{{ route('admin.lieux.destroy', $lieu) }}" method="POST" style="display:inline" onsubmit="return confirm('Supprimer ce lieu ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Suppr</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Aucun lieu trouvé.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-3">
            {{ $lieux->links() }}
        </div>
    </div>
@endsection
