<?php

use App\Http\Controllers\Admin\ArticleStatsController;
use Illuminate\Support\Facades\Route;

// Models
use App\Models\Cause;
use App\Models\Event;
use App\Models\Workshop;

// Front controllers
use App\Http\Controllers\CauseController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\EventController as FrontEventController;
use App\Http\Controllers\WorkshopController as FrontWorkshopController;
use App\Http\Controllers\Admin\UserController;

// Admin controllers
use App\Http\Controllers\Admin\CauseController as CauseControllerAdmin;
use App\Http\Controllers\Admin\LieuController  as AdminLieuController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\WorkshopController as AdminWorkshopController;
use App\Http\Controllers\Admin\MaterialController as AdminMaterialController;

// Auth controllers (manual)
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;


use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CategoryController as FrontCategoryController;

use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\TaskController    as AdminTaskController;
use App\Http\Controllers\ProjectController;

use App\Models\Donation;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Lieu;

use App\Http\Controllers\Admin\EventStatsController;


use App\Http\Controllers\Admin\ProjectStatsController;


use App\Http\Controllers\ChatbotController;


use App\Http\Controllers\DonationCheckoutController;
use App\Http\Controllers\StripeWebhookController;


use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;


/*
|--------------------------------------------------------------------------
| Home (front)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    // Featured sections (unchanged)
    $featuredCauses = Cause::take(3)->get();

    $featuredEvents = Event::with('lieu')
        ->orderBy('start_at', 'asc')
        ->take(6)
        ->get();

    $featuredWorkshops = Workshop::withCount('materials')
        ->latest()
        ->take(6)
        ->get();

    // ====== Donation Stats ======
    $totalRaised   = (float) Donation::sum('amount');
    $donationsCnt  = (int)   Donation::count();
    $avgDonation   = $donationsCnt > 0 ? $totalRaised / $donationsCnt : 0.0;

    $totalGoal     = (float) Cause::sum('goal_amount');
    $globalPercent = $totalGoal > 0 ? min(100, round(($totalRaised / $totalGoal) * 100)) : 0;

    // Top causes by amount raised
    $topCauses = Cause::withSum('donations', 'amount')
        ->orderByDesc('donations_sum_amount')
        ->take(5)
        ->get();

    // Recent donations
    $recentDonations = Donation::latest('date')->take(6)->get();

    // Donations per month for last 12 months
    $from = now()->subMonths(11)->startOfMonth();
    $rawMonthly = Donation::selectRaw('DATE_FORMAT(`date`, "%Y-%m") as ym, SUM(amount) as total')
        ->whereDate('date', '>=', $from)
        ->groupBy('ym')
        ->orderBy('ym')
        ->get()
        ->keyBy('ym');

    // Build complete 12-month series (ensures missing months show as 0)
    $labels = [];
    $series = [];
    for ($i = 0; $i < 12; $i++) {
        $month = $from->copy()->addMonths($i);
        $ym = $month->format('Y-m');
        $labels[] = $month->isoFormat('MMM YYYY');
        $series[] = (float) ($rawMonthly[$ym]->total ?? 0);
    }






    // ---- Workshops Stats (no registrations) ----
    $now = now();

    $totalWorkshops = (int) Workshop::count();
    $publishedCount = (int) Workshop::where('status','published')->count();
    $upcomingCount  = (int) Workshop::where('status','published')->where('start_at','>=',$now)->count();
    $pastCount      = (int) Workshop::where('status','published')->where('start_at','<',$now)->count();

    $totalCapacity  = (int) Workshop::sum('capacity');
    $avgCapacity    = (int) round(Workshop::avg('capacity'));

    // Avg materials per workshop (uses your many-to-many)
    $avgMaterials   = (int) round(
        (Workshop::withCount('materials')->get()->avg('materials_count')) ?? 0
    );

    // Top venues by upcoming published workshops
    $topVenues = Lieu::withCount(['workshops as upcoming_count' => function($q) use ($now) {
        $q->where('status','published')->where('start_at','>=',$now);
    }])
        ->orderByDesc('upcoming_count')
        ->take(5)
        ->get();

    // Top workshops by materials count
    $topWorkshops = Workshop::withCount('materials')
        ->orderByDesc('materials_count')
        ->take(5)
        ->get();

    // Workshops per month (last 12 months)
    $from = $now->copy()->subMonths(11)->startOfMonth();
    $raw = Workshop::selectRaw('DATE_FORMAT(start_at, "%Y-%m") as ym, COUNT(*) as cnt')
        ->where('status','published')
        ->where('start_at','>=',$from)
        ->groupBy('ym')->orderBy('ym')->get()->keyBy('ym');

    $labels = [];
    $seriesWorkshops = [];
    for ($i = 0; $i < 12; $i++) {
        $month = $from->copy()->addMonths($i);
        $ym    = $month->format('Y-m');
        $labels[] = $month->isoFormat('MMM YYYY');
        $seriesWorkshops[] = (int) ($raw[$ym]->cnt ?? 0);
    }




    return view('home', compact(
        'featuredCauses', 'featuredEvents', 'featuredWorkshops',
        'totalRaised', 'donationsCnt', 'avgDonation',
        'totalGoal', 'globalPercent',
        'topCauses', 'recentDonations',
        'labels', 'series',
        // stats vars for workshops:
        'totalWorkshops','publishedCount','upcomingCount','pastCount',
        'totalCapacity','avgCapacity','avgMaterials',
        'labels','seriesWorkshops','topVenues','topWorkshops'
    ));
})->name('home');


/*
|--------------------------------------------------------------------------
| Public / Front routes
|--------------------------------------------------------------------------
*/

// Donation checkout routes (using Stripe)
Route::post('/causes/{cause}/donate/checkout', [DonationCheckoutController::class, 'create'])
    ->name('causes.donations.checkout');

Route::get('/donations/success', [DonationCheckoutController::class, 'success'])->name('donations.success');
Route::get('/donations/cancel',  [DonationCheckoutController::class, 'cancel'])->name('donations.cancel');

// webhook endpoint (must be POST)
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])
    ->withoutMiddleware([VerifyCsrfToken::class])
    ->name('stripe.webhook');



Route::resource('causes', CauseController::class)->only(['index','show']);
Route::post('causes/{cause}/donations', [DonationController::class, 'store'])
    ->name('causes.donations.store');

Route::get('/events', [FrontEventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [FrontEventController::class, 'show'])->name('events.show');

Route::get('/workshops', [FrontWorkshopController::class,'index'])->name('workshops.index');
Route::get('/workshops/{workshop}', [FrontWorkshopController::class,'show'])->name('workshops.show');


Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('articles.show');
Route::get('/categories/{category}', [FrontCategoryController::class, 'show'])->name('categories.show');


Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');



Route::prefix('api')
    ->middleware('api') // gives you stateless API middleware (no CSRF)
    ->group(function () {
        Route::post('chatbot', [ChatbotController::class, 'chat'])->name('chatbot.chat');
        Route::options('chatbot', fn() => response()->noContent());
    });




/*
|--------------------------------------------------------------------------
| Auth (manual, no Breeze)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login',    [LoginController::class, 'create'])->name('login');
    Route::post('/login',   [LoginController::class, 'store']);
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register',[RegisterController::class, 'store']);
});
Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Admin (protected)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth','can:admin'])
    ->group(function () {



        // Project statistics dashboard
        Route::get('projects/stats', [ProjectStatsController::class, 'index'])
            ->name('projects.stats');


        // Event statistics dashboard
        Route::get('events/stats', [EventStatsController::class, 'index'])
            ->name('events.stats');

        // Article statistics dashboard
        Route::get('articles/stats', [ArticleStatsController::class, 'index'])
            ->name('articles.stats');


        // Users management
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::patch('users/{user}/ban', [UserController::class, 'ban'])->name('users.ban');
        Route::patch('users/{user}/unban', [UserController::class, 'unban'])->name('users.unban');

        // Causes (back-office)
        Route::resource('causes', CauseControllerAdmin::class);

        // Lieux
        Route::resource('lieux', AdminLieuController::class)->parameters([
            'lieux' => 'lieu',
        ]);

        // Events
        Route::resource('events', AdminEventController::class);

        // Workshops & Materials

        Route::resource('workshops', AdminWorkshopController::class);
        Route::resource('materials', AdminMaterialController::class)->except(['show']);

        // article & category
        Route::resource('categories', AdminCategoryController::class);
        Route::resource('articles',  AdminArticleController::class);

        Route::resource('projects', AdminProjectController::class);
        Route::resource('tasks',    AdminTaskController::class);






    });

/*
|--------------------------------------------------------------------------
| (Optional) Fallback for 404
|--------------------------------------------------------------------------
*/
 Route::fallback(fn() => abort(404));



// End of routes/web.php
