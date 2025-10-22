<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ArticleStatsController extends Controller
{
    public function index()
    {
        // KPIs
        $totalArticles   = (int) Article::count();
        $publishedCount  = (int) Article::where('is_published', true)->count();
        $draftCount      = (int) Article::where('is_published', false)->count();
        $avgPerCategory  = round(Category::has('articles')->withCount('articles')->get()->avg('articles_count'), 1);

        // Top categories by article count
        $topCategories = Category::withCount('articles')
            ->orderByDesc('articles_count')
            ->take(5)
            ->get();

        // Monthly published articles (last 12 months)
        $from = now()->subMonths(11)->startOfMonth();
        $raw = Article::selectRaw('DATE_FORMAT(published_at, "%Y-%m") as ym, COUNT(*) as cnt')
            ->where('is_published', true)
            ->where('published_at', '>=', $from)
            ->groupBy('ym')
            ->orderBy('ym')
            ->get()
            ->keyBy('ym');

        $labels = [];
        $series = [];
        for ($i = 0; $i < 12; $i++) {
            $month = $from->copy()->addMonths($i);
            $ym = $month->format('Y-m');
            $labels[] = $month->isoFormat('MMM YYYY');
            $series[] = (int) ($raw[$ym]->cnt ?? 0);
        }

        // Recent articles
        $recentArticles = Article::where('is_published', true)
            ->latest('published_at')
            ->take(6)
            ->get(['id', 'title', 'published_at']);

        return view('admin.articles.articlesStats', compact(
            'totalArticles','publishedCount','draftCount','avgPerCategory',
            'topCategories','labels','series','recentArticles'
        ));
    }
}
