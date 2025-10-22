<?php

namespace App\Http\Controllers;
use App\Models\Workshop;

class WorkshopController extends Controller
{
    public function index()
    {
        $workshops = Workshop::with('lieu')
            ->where('status', 'published')
            ->orderBy('start_at')
            ->paginate(9);

        return view('workshops.index', compact('workshops'));
    }

    public function show(Workshop $workshop)
    {
        abort_unless($workshop->status === 'published', 404);
        $workshop->load(['lieu', 'materials']);
        return view('workshops.show', compact('workshop'));
    }
}
