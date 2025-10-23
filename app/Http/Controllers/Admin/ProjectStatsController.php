<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Facades\DB;

class ProjectStatsController extends Controller
{
    public function index()
    {
        $now = now();

        // ----- KPIs -----
        $totalProjects = (int) Project::count();

        $plannedCount   = (int) Project::where('status','planned')->count();
        $activeCount    = (int) Project::where('status','active')->count();
        $completedCount = (int) Project::where('status','completed')->count();
        $archivedCount  = (int) Project::where('status','archived')->count();

        $avgProgress = (int) round(Project::avg('progress'));

        // Average duration (days) for completed projects with both dates
        $avgDurationDays = (int) (Project::where('status','completed')
            ->whereNotNull('start_date')
            ->whereNotNull('end_date')
            ->select(DB::raw('AVG(DATEDIFF(end_date, start_date)) as avg_days'))
            ->value('avg_days') ?? 0);

        // ----- Top projects by OPEN tasks -----
        // (status != 'done' — adjust if your Task statuses differ)
        $topProjectsOpenTasks = Project::withCount([
            'tasks as open_tasks_count' => fn($q) => $q->where('status','!=','done')
        ])
            ->orderByDesc('open_tasks_count')
            ->take(5)
            ->get(['id','title','status','progress']);

        // ----- Monthly CREATED projects (last 12 months) -----
        $from = $now->copy()->subMonths(11)->startOfMonth();

        $rawCreated = Project::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as ym, COUNT(*) as cnt')
            ->where('created_at','>=',$from)
            ->groupBy('ym')
            ->orderBy('ym')
            ->get()
            ->keyBy('ym');

        // ----- Monthly COMPLETED projects (last 12 months) -----
        // We’ll approximate “completed per month” using end_date month where status is completed.
        $rawCompleted = Project::selectRaw('DATE_FORMAT(end_date, "%Y-%m") as ym, COUNT(*) as cnt')
            ->where('status','completed')
            ->whereNotNull('end_date')
            ->where('end_date','>=',$from)
            ->groupBy('ym')
            ->orderBy('ym')
            ->get()
            ->keyBy('ym');

        $labels = [];
        $seriesCreated   = [];
        $seriesCompleted = [];
        for ($i = 0; $i < 12; $i++) {
            $m  = $from->copy()->addMonths($i);
            $ym = $m->format('Y-m');
            $labels[]         = $m->isoFormat('MMM YYYY');
            $seriesCreated[]  = (int) ($rawCreated[$ym]->cnt ?? 0);
            $seriesCompleted[] = (int) ($rawCompleted[$ym]->cnt ?? 0);
        }

        // ----- Recent projects -----
        $recentProjects = Project::latest()
            ->take(8)
            ->get(['id','title','status','progress','start_date','end_date']);

        return view('admin.projects.projectsStats', compact(
            'totalProjects','plannedCount','activeCount','completedCount','archivedCount',
            'avgProgress','avgDurationDays',
            'labels','seriesCreated','seriesCompleted',
            'topProjectsOpenTasks','recentProjects'
        ));
    }
}
