<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::where('status','!=','archived')
            ->orderByDesc('created_at')
            ->paginate(9);

        return view('projects.index', compact('projects'));
    }

    public function show(Project $project)
    {
        // Liste les tâches par "order" puis date
        $tasks = $project->tasks()
            ->orderBy('order')
            ->orderBy('due_date')
            ->get();

        return view('projects.show', compact('project','tasks'));
    }
}
