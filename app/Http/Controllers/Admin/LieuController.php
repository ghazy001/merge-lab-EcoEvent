<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lieu;
use Illuminate\Http\Request;

class LieuController extends Controller
{
    public function index()
    {
        $lieux = Lieu::orderBy('name')->paginate(15);
        return view('admin.lieux.index', compact('lieux'));
    }

    public function create()
    {
        return view('admin.lieux.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
        ]);

        Lieu::create($data);

        return redirect()->route('admin.lieux.index')->with('success', 'Lieu créé avec succès.');
    }

    public function edit(Lieu $lieu)
    {
        return view('admin.lieux.edit', compact('lieu'));
    }

    public function update(Request $request, Lieu $lieu)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $lieu->update($data);

        return redirect()->route('admin.lieux.index')->with('success', 'Lieu mis à jour.');
    }

    public function destroy(Lieu $lieu)
    {
        $lieu->delete();
        return back()->with('success', 'Lieu supprimé.');
    }
}
