<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::with('category')
            ->where('is_published', true)
            ->orderByDesc('published_at')
            ->paginate(9);

        return view('articles.index', compact('articles'));
    }

    public function show(Article $article)
    {
        abort_unless($article->is_published, 404);
        return view('articles.show', compact('article'));
    }
}
