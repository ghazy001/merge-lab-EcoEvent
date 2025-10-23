<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $q = Task::with('project')->latest();

        if ($request->filled('project_id')) {
            $q->where('project_id', $request->integer('project_id'));
        }

        $tasks = $q->paginate(15);
        $projects = Project::orderBy('title')->pluck('title','id');

        return view('admin.tasks.index', compact('tasks','projects'));
    }

    public function create(Request $request)
    {
        $projects = Project::orderBy('title')->pluck('title','id');
        $selectedProject = $request->integer('project'); // preselect via ?project=ID
        return view('admin.tasks.create', compact('projects','selectedProject'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id'  => ['required','exists:projects,id'],
            'title'       => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'status'      => ['required','in:todo,doing,done'],
            'due_date'    => ['nullable','date'],
            'order'       => ['nullable','integer','min:0','max:65535'],
        ]);

        $data['order'] = $data['order'] ?? 0;

        Task::create($data);

        return redirect()->route('admin.tasks.index', ['project_id' => $data['project_id']])
            ->with('success','Tâche créée.');
    }

    public function show(Task $task)
    {
        return view('admin.tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $projects = Project::orderBy('title')->pluck('title','id');
        return view('admin.tasks.edit', compact('task','projects'));
    }

    public function update(Request $request, Task $task)
    {
        $data = $request->validate([
            'project_id'  => ['required','exists:projects,id'],
            'title'       => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'status'      => ['required','in:todo,doing,done'],
            'due_date'    => ['nullable','date'],
            'order'       => ['nullable','integer','min:0','max:65535'],
        ]);

        $task->update($data);

        return redirect()->route('admin.tasks.index', ['project_id' => $data['project_id']])
            ->with('success','Tâche mise à jour.');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return back()->with('success','Tâche supprimée.');
    }
}
