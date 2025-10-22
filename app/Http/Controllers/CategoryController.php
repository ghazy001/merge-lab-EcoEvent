<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function show(Category $category)
    {
        $articles = $category->articles()
            ->where('is_published', true)
            ->orderByDesc('published_at')
            ->paginate(9);

        return view('categories.show', compact('category','articles'));
    }
}
