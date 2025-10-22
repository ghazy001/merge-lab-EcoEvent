<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CauseController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) ($request->input('per_page', 10));
        $perPage = in_array($perPage, [10,15,25,50], true) ? $perPage : 10;

        $causes = Cause::query()
            ->withSum('donations as total_donations', 'amount') // alias to what the view expects
            ->search($request->input('q'))
            ->status($request->input('status'))
            ->goalBetween($request->input('min_goal'), $request->input('max_goal'))
            ->hasImage($request->input('has_image'))
            ->sortBy($request->input('sort'))
            ->paginate($perPage)
            ->withQueryString();

        // For the status filter select
        $statuses = ['active','completed','canceled'];

        return view('admin.causes.index', compact('causes','statuses'));
    }

    public function create()
    {
        return view('admin.causes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required',
            'description'  => 'nullable|string',
            'goal_amount'  => 'required|numeric|min:0',
            'status'       => 'required|in:active,completed,canceled',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // handle file
        if ($request->hasFile('image')) {
            // store like: storage/app/public/causes/xxx.jpg
            $data['image_path'] = $request->file('image')->store('causes', 'public');
        }

        Cause::create($data);

        return redirect()->route('admin.causes.index')->with('success','Cause created.');
    }

    public function edit(Cause $cause)
    {
        return view('admin.causes.edit', compact('cause'));
    }

    public function update(Request $request, Cause $cause)
    {
        $data = $request->validate([
            'title'        => 'required',
            'description'  => 'nullable|string',
            'goal_amount'  => 'required|numeric|min:0',
            'status'       => 'required|in:active,completed,canceled',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'remove_image' => 'nullable|boolean',
        ]);

        // optional: delete old image if requested
        if ($request->boolean('remove_image') && $cause->image_path) {
            Storage::disk('public')->delete($cause->image_path);
            $data['image_path'] = null;
        }

        // replace with a new upload
        if ($request->hasFile('image')) {
            if ($cause->image_path) {
                Storage::disk('public')->delete($cause->image_path);
            }
            $data['image_path'] = $request->file('image')->store('causes', 'public');
        }

        $cause->update($data);

        return redirect()->route('admin.causes.index')->with('success','Cause updated.');
    }

    public function destroy(Cause $cause)
    {
        $cause->delete(); // booted() handles file deletion
        return redirect()->route('admin.causes.index')->with('success','Cause deleted.');
    }
}
