<?php
// app/Http/Controllers/Admin/MaterialController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Material;

class MaterialController extends Controller
{
    public function index()
    {
        $materials = Material::latest()->paginate(10);
        return view('admin.materials.index', compact('materials'));
    }

    public function create()
    {
        return view('admin.materials.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
            'unit'  => 'nullable|string|max:20',
        ]);

        Material::create($data);
        return redirect()->route('admin.materials.index')->with('success','Matériel créé.');
    }

    public function edit(Material $material)
    {
        return view('admin.materials.edit', compact('material'));
    }

    public function update(Request $request, Material $material)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
            'unit'  => 'nullable|string|max:20',
        ]);

        $material->update($data);
        return redirect()->route('admin.materials.index')->with('success','Matériel mis à jour.');
    }

    public function destroy(Material $material)
    {
        $material->delete(); // FK cascade sur pivot
        return back()->with('success','Matériel supprimé.');
    }
}
