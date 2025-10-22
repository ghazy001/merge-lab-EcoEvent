<?php
// app/Http/Controllers/Admin/WorkshopController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Workshop;
use App\Models\Lieu;
use App\Models\Material;

class WorkshopController extends Controller
{

    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);
        $perPage = in_array($perPage, [10,15,25,50], true) ? $perPage : 10;

        $workshops = Workshop::query()
            ->with(['lieu','materials'])
            ->search($request->input('q'))
            ->statusIs($request->input('status'))                 // draft | published
            ->atLieu($request->input('lieu_id'))
            ->period($request->input('period'))                   // upcoming|past|today|week|month
            ->startBetween($request->input('from'), $request->input('to'))
            ->capacityBetween($request->input('min_capacity'), $request->input('max_capacity'))
            ->hasMaterial($request->input('material_id'))         // single
            // ->hasAnyMaterials($request->input('material_ids', [])) // or many, if you enable multiselect in the view
            ->sortBy($request->input('sort'))
            ->paginate($perPage)
            ->withQueryString();

        $lieux = Lieu::orderBy('name')->get(['id','name']);
        $materials = Material::orderBy('name')->get(['id','name']);

        return view('admin.workshops.index', compact('workshops','lieux','materials'));
    }


    public function create()
    {
        $lieux = Lieu::orderBy('name')->get();
        $materials = Material::orderBy('name')->get();
        return view('admin.workshops.create', compact('lieux','materials'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'start_at'     => 'nullable|date',
            'end_at'       => 'nullable|date|after_or_equal:start_at',
            'lieu_id'      => 'nullable|exists:lieux,id',
            'capacity'     => 'nullable|integer|min:0',
            'status'       => 'required|in:draft,published',
            'material_ids'   => 'array',
            'material_ids.*' => 'exists:materials,id',
            'quantities'     => 'array',
            'quantities.*'   => 'nullable|integer|min:1',
        ]);

        $workshop = Workshop::create($data);

        // sync pivot quantities
        $sync = [];
        foreach (($request->input('material_ids', []) ?? []) as $mid) {
            $q = (int)($request->input("quantities.$mid") ?? 1);
            $sync[$mid] = ['quantity' => max(1, $q)];
        }
        $workshop->materials()->sync($sync);

        return redirect()->route('admin.workshops.index')->with('success','Workshop créé.');
    }

    public function edit(Workshop $workshop)
    {
        $lieux = Lieu::orderBy('name')->get();
        $materials = Material::orderBy('name')->get();
        $current = $workshop->materials->pluck('pivot.quantity','id'); // id => qty
        return view('admin.workshops.edit', compact('workshop','lieux','materials','current'));
    }

    public function update(Request $request, Workshop $workshop)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'start_at'     => 'nullable|date',
            'end_at'       => 'nullable|date|after_or_equal:start_at',
            'lieu_id'      => 'nullable|exists:lieux,id',
            'capacity'     => 'nullable|integer|min:0',
            'status'       => 'required|in:draft,published',
            'material_ids'   => 'array',
            'material_ids.*' => 'exists:materials,id',
            'quantities'     => 'array',
            'quantities.*'   => 'nullable|integer|min:1',
        ]);

        $workshop->update($data);

        $sync = [];
        foreach (($request->input('material_ids', []) ?? []) as $mid) {
            $q = (int)($request->input("quantities.$mid") ?? 1);
            $sync[$mid] = ['quantity' => max(1, $q)];
        }
        $workshop->materials()->sync($sync);

        return redirect()->route('admin.workshops.index')->with('success','Workshop mis à jour.');
    }

    public function destroy(Workshop $workshop)
    {
        $workshop->materials()->detach();
        $workshop->delete();
        return back()->with('success','Workshop supprimé.');
    }
}
