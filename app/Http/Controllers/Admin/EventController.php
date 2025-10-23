<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Lieu;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 15);
        $perPage = in_array($perPage, [10,15,25,50], true) ? $perPage : 15;

        $events = Event::with('lieu')
            ->search($request->input('q'))
            ->atLieu($request->input('lieu_id'))
            ->period($request->input('period'))
            ->startBetween($request->input('from'), $request->input('to'))
            ->capacityBetween($request->input('min_capacity'), $request->input('max_capacity'))
            ->sortBy($request->input('sort'))
            ->paginate($perPage)
            ->withQueryString();

        $lieux = Lieu::orderBy('name')->get(['id','name']);

        return view('admin.events.index', compact('events','lieux'));
    }

    public function create()
    {
        // liste pour le select dans le formulaire
        $lieux = Lieu::orderBy('name')->pluck('name','id');
        return view('admin.events.create', compact('lieux'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_at' => 'required|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'lieu_id' => 'required|exists:lieux,id',
            'capacity' => 'nullable|integer|min:0',
        ]);

        Event::create($data);

        return redirect()->route('admin.events.index')->with('success', 'Événement créé.');
    }

    public function edit(Event $event)
    {
        $lieux = Lieu::orderBy('name')->pluck('name','id');
        return view('admin.events.edit', compact('event','lieux'));
    }

    public function update(Request $request, Event $event)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_at' => 'required|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'lieu_id' => 'required|exists:lieux,id',
            'capacity' => 'nullable|integer|min:0',
        ]);

        $event->update($data);

        return redirect()->route('admin.events.index')->with('success', 'Événement mis à jour.');
    }


    public function destroy(Event $event)
    {
        $event->delete(); // soft delete si trait présent
        return back()->with('success','Événement mis à la corbeille.');
    }

    public function trashed() // route admin.events.trashed
    {
        $events = Event::onlyTrashed()->paginate(20);
        return view('admin.events.trashed', compact('events'));
    }

    public function restore($id)
    {
        $event = Event::onlyTrashed()->findOrFail($id);
        $event->restore();
        return back()->with('success','Événement restauré.');
    }

    public function forceDelete($id)
    {
        $event = Event::onlyTrashed()->findOrFail($id);
        $event->forceDelete();
        return back()->with('success','Événement définitivement supprimé.');
    }



}
