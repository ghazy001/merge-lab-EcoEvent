<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{

    public function index(Request $request)
    {
        $articles = Article::with('category')
            ->search($request->input('q'))                // string|null
            ->categoryId($request->input('category_id'))  // string|int|null
            ->status($request->input('status'))           // string|null
            ->publishedBetween($request->input('from'), $request->input('to'))
            ->latest('published_at')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categories = Category::orderBy('name')->get(['id', 'name']);

        return view('admin.articles.index', compact('articles', 'categories'));
    }


    public function create()
    {
        $categories = Category::orderBy('name')->pluck('name','id');
        return view('admin.articles.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id'  => ['nullable','exists:categories,id'],
            'title'        => ['required','string','max:255'],
            'excerpt'      => ['nullable','string','max:255'],
            'body'         => ['required','string'],
            'image'        => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
            'is_published' => ['sometimes','boolean'],
            'published_at' => ['nullable','date'],
        ]);

        $data['slug'] = $this->makeUniqueSlug($data['title'], 'articles');
        $data['is_published'] = (bool)($data['is_published'] ?? false);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('articles','public');
        }

        if ($data['is_published'] && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $article = Article::create($data);

        return redirect()->route('admin.articles.edit', $article)
            ->with('success','Article créé.');
    }

    public function show(Article $article)
    {
        return view('admin.articles.show', compact('article'));
    }

    public function edit(Article $article)
    {
        $categories = Category::orderBy('name')->pluck('name','id');
        return view('admin.articles.edit', compact('article','categories'));
    }

    public function update(Request $request, Article $article)
    {
        $data = $request->validate([
            'category_id'  => ['nullable','exists:categories,id'],
            'title'        => ['required','string','max:255'],
            'excerpt'      => ['nullable','string','max:255'],
            'body'         => ['required','string'],
            'image'        => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
            'is_published' => ['sometimes','boolean'],
            'published_at' => ['nullable','date'],
        ]);

        if ($article->title !== $data['title']) {
            $data['slug'] = $this->makeUniqueSlug($data['title'], 'articles', $article->id);
        }

        if ($request->hasFile('image')) {
            if ($article->image_path) {
                Storage::disk('public')->delete($article->image_path);
            }
            $data['image_path'] = $request->file('image')->store('articles','public');
        }

        $data['is_published'] = (bool)($data['is_published'] ?? false);

        if ($data['is_published'] && empty($data['published_at'])) {
            $data['published_at'] = now();
        }
        if (!$data['is_published']) {
            $data['published_at'] = null;
        }

        $article->update($data);

        return redirect()->route('admin.articles.index')
            ->with('success','Article mis à jour.');
    }

    public function destroy(Article $article)
    {
        if ($article->image_path) {
            Storage::disk('public')->delete($article->image_path);
        }
        $article->delete();
        return back()->with('success','Article supprimé.');
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
