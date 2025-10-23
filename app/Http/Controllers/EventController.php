<?php

namespace App\Http\Controllers;

use App\Models\Event;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::with('lieu')->orderBy('start_at','asc')->paginate(12);
        return view('events.index', compact('events'));
    }

    public function show(Event $event)
    {
        $event->load('lieu');
        return view('   events.show', compact('event'));
    }
}
