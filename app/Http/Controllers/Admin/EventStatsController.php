<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Lieu;

class EventStatsController extends Controller
{
    public function index()
    {
        $now = now();

        // KPIs
        $totalEvents    = (int) Event::count();
        $upcomingCount  = (int) Event::where('start_at', '>=', $now)->count();
        $pastCount      = (int) Event::where('start_at', '<', $now)->count();
        $trashedCount   = (int) Event::onlyTrashed()->count();

        $totalCapacity  = (int) Event::sum('capacity');
        $avgCapacity    = (int) round(Event::avg('capacity'));

        // Top venues by UPCOMING events
        $topVenues = Lieu::withCount(['events as upcoming_count' => function ($q) use ($now) {
            $q->where('start_at', '>=', $now);
        }])
            ->orderByDesc('upcoming_count')
            ->take(5)
            ->get();

        // Events per month (last 12 months, based on start_at)
        $from = $now->copy()->subMonths(11)->startOfMonth();
        $raw = Event::selectRaw('DATE_FORMAT(start_at, "%Y-%m") as ym, COUNT(*) as cnt')
            ->where('start_at', '>=', $from)
            ->groupBy('ym')
            ->orderBy('ym')
            ->get()
            ->keyBy('ym');

        $labels = [];
        $series = [];
        for ($i = 0; $i < 12; $i++) {
            $m = $from->copy()->addMonths($i);
            $ym = $m->format('Y-m');
            $labels[] = $m->isoFormat('MMM YYYY');
            $series[] = (int) ($raw[$ym]->cnt ?? 0);
        }

        // Recent events (upcoming first; otherwise latest past)
        $recentEvents = Event::orderBy('start_at', 'desc')
            ->take(8)
            ->get(['id','title','start_at','end_at','lieu_id']);

        return view('admin.events.eventsStats', compact(
            'totalEvents','upcomingCount','pastCount','trashedCount',
            'totalCapacity','avgCapacity','labels','series','topVenues','recentEvents'
        ));
    }
}
