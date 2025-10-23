<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->paginate(12);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required','string','max:255','unique:categories,name'],
            'description' => ['nullable','string'],
        ]);

        $data['slug'] = $this->makeUniqueSlug($data['name'], 'categories');

        Category::create($data);

        return redirect()->route('admin.categories.index')
            ->with('success','Catégorie créée.');
    }

    public function show(Category $category)
    {
        return view('admin.categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name'        => ['required','string','max:255','unique:categories,name,'.$category->id],
            'description' => ['nullable','string'],
        ]);

        if ($category->name !== $data['name']) {
            $data['slug'] = $this->makeUniqueSlug($data['name'], 'categories', $category->id);
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')
            ->with('success','Catégorie mise à jour.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return back()->with('success','Catégorie supprimée.');
    }

    /** Slug helper */
    private function makeUniqueSlug(string $base, string $table, ?int $ignoreId = null): string
    {
        $slug = Str::slug($base);
        $original = $slug;
        $i = 2;
        while (
        DB::table($table)
            ->when($ignoreId, fn($q) => $q->where('id','!=',$ignoreId))
            ->where('slug',$slug)->exists()
        ) {
            $slug = "{$original}-{$i}";
            $i++;
        }
        return $slug;
    }
}
