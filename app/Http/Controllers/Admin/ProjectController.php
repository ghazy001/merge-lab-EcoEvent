<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 12);
        $perPage = in_array($perPage, [12,24,50,100], true) ? $perPage : 12;

        $projects = Project::query()
            // eager count for open tasks (used in filter/sort and display)
            ->withCount([
                'tasks as open_tasks_count' => fn($q) => $q->where('status', '!=', 'done')
            ])
            ->search($request->input('q'))
            ->status($request->input('status'))
            ->progressBetween($request->input('min_progress'), $request->input('max_progress'))
            ->dateBetween($request->input('from'), $request->input('to'))
            ->openTasksBetween($request->input('min_open'), $request->input('max_open'))
            ->sortBy($request->input('sort'))
            ->paginate($perPage)
            ->withQueryString();

        $statuses = ['planned','active','completed','archived'];

        return view('admin.projects.index', compact('projects','statuses'));
    }

    public function create()
    {
        return view('admin.projects.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:planned,active,completed,archived'],
            'progress' => ['nullable', 'integer', 'between:0,100'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $data['slug'] = $this->makeUniqueSlug($data['title'], 'projects');
        $data['progress'] = $data['progress'] ?? 0;

        $project = Project::create($data);

        return redirect()->route('admin.projects.edit', $project)
            ->with('success', 'Projet créé.');
    }

    public function show(Project $project)
    {
        $project->load('tasks');
        return view('admin.projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        return view('admin.projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:planned,active,completed,archived'],
            'progress' => ['nullable', 'integer', 'between:0,100'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        if ($project->title !== $data['title']) {
            $data['slug'] = $this->makeUniqueSlug($data['title'], 'projects', $project->id);
        }

        $project->update($data);

        return redirect()->route('admin.projects.index')
            ->with('success', 'Projet mis à jour.');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return back()->with('success', 'Projet supprimé.');
    }

    /** Slug helper */
    private function makeUniqueSlug(string $base, string $table, ?int $ignoreId = null): string
    {
        $slug = Str::slug($base);
        $original = $slug;
        $i = 2;
        while (
        DB::table($table)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $slug)->exists()
        ) {
            $slug = "{$original}-{$i}";
            $i++;
        }
        return $slug;
    }
}
