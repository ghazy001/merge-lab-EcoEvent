<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;
use Carbon\Carbon;

// Models
use App\Models\Event;
use App\Models\Workshop;
use App\Models\Cause;
use App\Models\Donation;

class ChatbotController extends Controller
{
    /**
     * POST /api/chatbot
     * Body: { messages: [{role:"user|model", text:"..."}] }
     */
    public function chat(Request $request)
    {
        $data = $request->validate([
            'messages' => 'required|array|min:1',
        ]);

        $apiKey = env('GEMINI_API_KEY');
        abort_unless($apiKey, 500, 'Gemini API key missing');

        $tz = 'Africa/Tunis';
        $messages = $data['messages'];
        $lastUserText = (string)($messages[array_key_last($messages)]['text'] ?? '');

        // 1) Try to normalize to a supported intent
        $intent = $this->extractIntent($apiKey, $lastUserText, $tz);

        if ($intent && in_array(($intent['type'] ?? ''), $this->supportedIntents(), true)) {
            $handled = $this->executeIntent($intent, $tz);
            if (($handled['ok'] ?? false) === true) {
                return response()->json(['reply' => $handled['reply']], 200);
            }
        }

        // 2) Fallback: small DB context + normal chat
        $context = $this->buildTinyContext();
        $contents = [];

        if ($context !== '') {
            $contents[] = [
                'role'  => 'user',
                'parts' => [[
                    'text' =>
                        "CONTEXT (from our database):\n".
                        "----------------------------\n".
                        $context."\n".
                        "----------------------------\n".
                        "Answer using only these facts when relevant. If unknown, say you’re not sure."
                ]],
            ];
        }

        foreach ($messages as $m) {
            $contents[] = [
                'role'  => ($m['role'] === 'model') ? 'model' : 'user',
                'parts' => [['text' => (string)($m['text'] ?? '')]],
            ];
        }

        $endpoint = "https://generativelanguage.googleapis.com/v1/models/gemini-2.5-flash:generateContent";

        try {
            $resp = Http::retry(2, 300)
                ->timeout(30)
                ->withHeaders([
                    'Content-Type'   => 'application/json',
                    'x-goog-api-key' => $apiKey,
                ])
                ->post($endpoint, [
                    'contents' => $contents,
                ]);

            $json = $resp->json();
            if (isset($json['error'])) {
                $msg = $json['error']['message'] ?? 'Unknown provider error.';
                return response()->json(['reply' => "Provider error: {$msg}"], 200);
            }

            $candidate = $json['candidates'][0] ?? null;
            foreach (($candidate['content']['parts'] ?? []) as $p) {
                if (!empty($p['text'])) {
                    return response()->json(['reply' => $p['text']], 200);
                }
            }

            return response()->json(['reply' => "I couldn't find an answer right now."], 200);

        } catch (\Throwable $e) {
            return response()->json(['reply' => 'Unexpected server error talking to the AI provider.'], 200);
        }
    }

    /**
     * Intents your backend supports (whitelist).
     */
    private function supportedIntents(): array
    {
        return [
            // Events
            'count_events_in_month',
            'list_events_between',
            'list_events_in_country',

            // Workshops
            'list_workshops',
            'count_workshops_in_country',

            // Causes & Donations
            'top_causes',
            'list_donations',
            'top_donor',
            'donations_stats_month',

            // Misc / small talk
            'greeting',
            'help',
        ];
    }

    /**
     * Execute a supported intent with Eloquent and return a deterministic reply.
     * Returns: ['ok'=>bool, 'reply'=>string]
     */
    private function executeIntent(array $intent, string $tz): array
    {
        $type = $intent['type'] ?? '';

        switch ($type) {
            // ========= EVENTS =========

            case 'count_events_in_month': {
                $month = (int)($intent['month'] ?? Carbon::now($tz)->month);
                $year  = (int)($intent['year']  ?? Carbon::now($tz)->year);
                $start = Carbon::create($year, $month, 1, 0, 0, 0, $tz);
                $end   = (clone $start)->endOfMonth();

                $count = Event::whereBetween('start_at', [$start, $end])->count();
                $reply = "We have {$count} event(s) in ".$start->isoFormat('MMMM')." {$year}.";
                return ['ok'=>true, 'reply'=>$reply];
            }

            case 'list_events_between': {
                $from = Arr::get($intent, 'date_range.from');
                $to   = Arr::get($intent, 'date_range.to');
                $from = $from ? Carbon::parse($from, $tz)->startOfDay() : Carbon::now($tz)->startOfDay();
                $to   = $to   ? Carbon::parse($to,   $tz)->endOfDay()   : Carbon::now($tz)->addMonth()->endOfDay();

                $items = Event::with('lieu')
                    ->whereBetween('start_at', [$from, $to])
                    ->orderBy('start_at')->take(10)
                    ->get(['id','title','start_at','lieu_id']);

                if ($items->isEmpty()) {
                    return ['ok'=>true, 'reply'=>"No events found between {$from->toDateString()} and {$to->toDateString()}."];
                }

                $lines = $items->map(fn($e)=>
                    "- #{$e->id} {$e->title} — ".($e->start_at?->toDayDateTimeString())." @ ".($e->lieu?->name ?? 'TBA')
                )->implode("\n");

                return ['ok'=>true, 'reply'=>"Events from {$from->toDateString()} to {$to->toDateString()}:\n{$lines}"];
            }

            case 'list_events_in_country': {
                $kw = strtolower($intent['countryKeyword'] ?? '');
                if ($kw === '') return ['ok'=>true,'reply'=>'Please specify a location.'];

                $items = Event::with('lieu')
                    ->whereHas('lieu', fn($q)=>
                    $q->whereRaw('LOWER(address) LIKE ?',["%{$kw}%"])
                        ->orWhereRaw('LOWER(name) LIKE ?',    ["%{$kw}%"])
                    )
                    ->orderBy('start_at')->take(10)
                    ->get(['id','title','start_at','lieu_id']);

                if ($items->isEmpty()) return ['ok'=>true,'reply'=>"No events found in ".ucfirst($kw)."."];
                $lines = $items->map(fn($e)=>
                    "- #{$e->id} {$e->title} — ".($e->start_at?->toDayDateTimeString())." @ ".($e->lieu?->name ?? 'TBA')
                )->implode("\n");

                return ['ok'=>true,'reply'=>"Events in ".ucfirst($kw).":\n{$lines}"];
            }

            // ========= WORKSHOPS =========

            case 'list_workshops': {
                $countryKw = Arr::get($intent, 'countryKeyword');
                $from      = Arr::get($intent, 'date_range.from');
                $to        = Arr::get($intent, 'date_range.to');
                $limit     = (int) (Arr::get($intent, 'limit', 10));

                $q = Workshop::query()->where('status','published');

                if ($countryKw) {
                    $kw = strtolower($countryKw);
                    $q->whereHas('lieu', function ($qq) use ($kw) {
                        $qq->whereRaw('LOWER(address) LIKE ?', ["%{$kw}%"])
                            ->orWhereRaw('LOWER(name) LIKE ?',    ["%{$kw}%"]);
                    });
                }

                if ($from || $to) {
                    $from = $from ? Carbon::parse($from, $tz)->startOfDay() : Carbon::now($tz)->startOfDay();
                    $to   = $to   ? Carbon::parse($to,   $tz)->endOfDay()   : Carbon::now($tz)->addMonths(6)->endOfDay();
                    $q->whereBetween('start_at', [$from, $to]);
                }

                $items = $q->with('lieu')->orderBy('start_at')->take($limit)->get(['id','title','start_at','capacity','lieu_id']);

                if ($items->isEmpty()) {
                    $where = $countryKw ? ' in '.ucfirst($countryKw) : '';
                    return ['ok'=>true, 'reply'=>"No published workshops{$where} found for that period."];
                }

                $lines = $items->map(fn($w)=>
                    "- #{$w->id} {$w->title} — ".($w->start_at?->toDayDateTimeString())." @ ".($w->lieu?->name ?? 'TBA')." (cap {$w->capacity})"
                )->implode("\n");

                $title = 'Workshops'.($countryKw ? ' in '.ucfirst($countryKw) : '');
                return ['ok'=>true, 'reply'=>"{$title}:\n{$lines}"];
            }

            case 'count_workshops_in_country': {
                $countryKw = strtolower((string)($intent['countryKeyword'] ?? ''));
                if ($countryKw === '') {
                    return ['ok'=>true,'reply'=>'Please provide a country keyword (e.g., "Tunisia").'];
                }

                $count = Workshop::where('status','published')
                    ->whereHas('lieu', fn($q)=>
                    $q->whereRaw('LOWER(address) LIKE ?', ["%{$countryKw}%"])
                        ->orWhereRaw('LOWER(name) LIKE ?',    ["%{$countryKw}%"])
                    )->count();

                return ['ok'=>true,'reply'=>"There are {$count} published workshop(s) in ".ucfirst($countryKw)."."];
            }

            // ========= CAUSES & DONATIONS =========

            case 'top_causes': {
                $limit = (int) (Arr::get($intent, 'limit', 5));
                $causes = Cause::withSum('donations','amount')
                    ->orderByDesc('donations_sum_amount')
                    ->take($limit)
                    ->get(['id','title','goal_amount']);

                if ($causes->isEmpty()) return ['ok'=>true, 'reply'=>'No causes found.'];

                $lines = $causes->map(function ($c) {
                    $raised = (float)($c->donations_sum_amount ?? 0);
                    $goal   = (float)($c->goal_amount ?? 0);
                    $pct    = $goal > 0 ? round(($raised / $goal) * 100) : 0;
                    return "- #{$c->id} {$c->title} — Raised ".number_format($raised, 2)." / Goal ".number_format($goal, 2)." ({$pct}%)";
                })->implode("\n");

                return ['ok'=>true, 'reply'=>"Top {$limit} causes by amount raised:\n{$lines}"];
            }

            case 'list_donations': {
                $limit = (int)($intent['limit'] ?? 5);
                $items = Donation::with('cause')
                    ->orderByDesc('date')->take($limit)
                    ->get(['id','donor_name','amount','date','cause_id']);

                if ($items->isEmpty()) return ['ok'=>true,'reply'=>'No donations found.'];

                $lines = $items->map(fn($d)=>
                    "- #{$d->id} {$d->donor_name} donated ".number_format((float)$d->amount,2)." on ".($d->date?->format('M d, Y'))." (cause: ".$d->cause?->title.")"
                )->implode("\n");

                return ['ok'=>true,'reply'=>"Recent donations:\n{$lines}"];
            }

            case 'top_donor': {
                $top = Donation::select('donor_name')
                    ->selectRaw('SUM(amount) as total')
                    ->groupBy('donor_name')
                    ->orderByDesc('total')
                    ->first();

                if (!$top) return ['ok'=>true,'reply'=>'No donors found.'];
                return ['ok'=>true,'reply'=>"Our top donor is {$top->donor_name}, contributing a total of ".number_format((float)$top->total,2)."!"];
            }

            case 'donations_stats_month': {
                $month = (int)($intent['month'] ?? Carbon::now($tz)->month);
                $year  = (int)($intent['year']  ?? Carbon::now($tz)->year);
                $start = Carbon::create($year, $month, 1, 0, 0, 0, $tz);
                $end   = (clone $start)->endOfMonth();

                $sum   = (float) Donation::whereBetween('date', [$start, $end])->sum('amount');
                $count = (int)   Donation::whereBetween('date', [$start, $end])->count();

                $reply = "Donations in ".$start->isoFormat('MMMM')." {$year}: {$count} donation(s) totaling ".number_format($sum, 2).".";
                return ['ok'=>true, 'reply'=>$reply];
            }

            // ========= MISC =========
            case 'greeting':
                return ['ok'=>true,'reply'=>'Hi there! 👋 How can I assist you with events, workshops, causes, or donations today?'];

            case 'help':
                return ['ok'=>true,'reply'=>"You can ask things like:\n- How many events in November?\n- Events between 2025-11-10 and 2025-11-20\n- Workshops in Tunisia next month\n- Top 5 causes\n- Recent donations\n- Who is the top donor?\n- How many workshops in Tunisia?\n- Events in Tunisia\nFeel free to ask in your own words!"];
        }

        return ['ok'=>false, 'reply'=>'Unsupported intent'];
    }

    /**
     * Ask Gemini to normalize the user's text into a compact JSON intent.
     * (No risky string interpolation; everything is concatenated.)
     */
    private function extractIntent(string $apiKey, string $userMsg, string $tz): ?array
    {
        $now = Carbon::now($tz);
        $exampleCountEventsYear = ($now->month <= 11) ? $now->year : $now->copy()->addYear()->year;
        $exampleBetweenYear     = $now->year;
        $nextMonthFrom          = $now->copy()->addMonth()->startOfMonth()->toDateString();
        $nextMonthTo            = $now->copy()->addMonth()->endOfMonth()->toDateString();

        // Build schema & examples without ${expr} interpolation
        $schema  = "Return ONLY JSON (no prose, no code fences). Use this schema:\n\n";
        $schema .= "{\n";
        $schema .= '  "type": "one of: count_events_in_month | list_events_between | list_events_in_country | list_workshops | count_workshops_in_country | top_causes | list_donations | top_donor | donations_stats_month | greeting | help | unknown",'."\n";
        $schema .= '  "month": 1-12 (optional),'."\n";
        $schema .= '  "year":  2000-2100 (optional),'."\n";
        $schema .= '  "countryKeyword": "lowercase keyword to match in lieux.address or lieux.name (e.g., "tunisia")" (optional),'."\n";
        $schema .= '  "date_range": { "from": "YYYY-MM-DD", "to": "YYYY-MM-DD" } (optional),'."\n";
        $schema .= '  "limit": 1-50 (optional)'."\n";
        $schema .= "}\n\n";
        $schema .= "Normalize the user's question to this schema. Use Africa/Tunis for implicit dates.\n";
        $schema .= "If unsure, return: {\"type\":\"unknown\"}.\n\n";
        $schema .= "EXAMPLES:\n";
        $schema .= "User: \"how many event we have in november?\"\n";
        $schema .= "{\"type\":\"count_events_in_month\",\"month\":11,\"year\":".$exampleCountEventsYear."}\n\n";
        $schema .= "User: \"list events from nov 10 to nov 20\"\n";
        $schema .= "{\"type\":\"list_events_between\",\"date_range\":{\"from\":\"".$exampleBetweenYear."-11-10\",\"to\":\"".$exampleBetweenYear."-11-20\"}}\n\n";
        $schema .= "User: \"events in tunisia\"\n";
        $schema .= "{\"type\":\"list_events_in_country\",\"countryKeyword\":\"tunisia\"}\n\n";
        $schema .= "User: \"workshops next month in tunisia\"\n";
        $schema .= "{\"type\":\"list_workshops\",\"countryKeyword\":\"tunisia\",\"date_range\":{\"from\":\"".$nextMonthFrom."\",\"to\":\"".$nextMonthTo."\"}}\n\n";
        $schema .= "User: \"top causes\"\n";
        $schema .= "{\"type\":\"top_causes\",\"limit\":5}\n\n";
        $schema .= "User: \"show donations\"\n";
        $schema .= "{\"type\":\"list_donations\",\"limit\":5}\n\n";
        $schema .= "User: \"best donor\" / \"top donor\"\n";
        $schema .= "{\"type\":\"top_donor\"}\n\n";
        $schema .= "User: \"donations in november 2025\"\n";
        theSchema_donations:
        $schema .= "{\"type\":\"donations_stats_month\",\"month\":11,\"year\":2025}\n\n";
        $schema .= "User: \"hello\" / \"hi\"\n";
        $schema .= "{\"type\":\"greeting\"}\n\n";
        $schema .= "User: \"help\" / \"what can you do\"\n";
        $schema .= "{\"type\":\"help\"}\n";

        $endpoint = "https://generativelanguage.googleapis.com/v1/models/gemini-2.5-flash:generateContent";

        try {
            $resp = Http::timeout(20)
                ->withHeaders(['Content-Type'=>'application/json','x-goog-api-key'=>$apiKey])
                ->post($endpoint, [
                    'contents' => [
                        ['role'=>'user', 'parts'=>[['text'=>$schema]]],
                        ['role'=>'user', 'parts'=>[['text'=>"User said: {$userMsg}\nReturn only JSON."]]],
                    ]
                ]);

            $json = $resp->json();
            $candidate = $json['candidates'][0] ?? null;
            foreach (($candidate['content']['parts'] ?? []) as $p) {
                if (!empty($p['text'])) {
                    $txt = trim($p['text']);
                    // In case the model adds fences
                    $txt = preg_replace('/^```json|```$/m', '', $txt);
                    $txt = trim($txt);
                    $parsed = json_decode($txt, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($parsed)) {
                        return $parsed;
                    }
                }
            }
        } catch (\Throwable $e) {
            // swallow; we'll fallback to normal chat
        }

        return null;
    }

    /**
     * Tiny DB context for fallback answers (kept short).
     */
    private function buildTinyContext(): string
    {
        $lines = [];

        $ev = Event::orderBy('start_at')->take(3)->get(['id','title','start_at']);
        if ($ev->isNotEmpty()) {
            $lines[] = "Upcoming events:\n" . $ev->map(fn($e)=>
                    "- #{$e->id} {$e->title} on ".($e->start_at?->toFormattedDateString())
                )->implode("\n");
        }

        $ws = Workshop::where('status','published')->orderBy('start_at')->take(3)->get(['id','title','start_at']);
        if ($ws->isNotEmpty()) {
            $lines[] = "Upcoming workshops:\n" . $ws->map(fn($w)=>
                    "- #{$w->id} {$w->title} on ".($w->start_at?->toFormattedDateString())
                )->implode("\n");
        }

        $cs = Cause::withSum('donations','amount')->orderByDesc('donations_sum_amount')->take(3)->get(['id','title','goal_amount']);
        if ($cs->isNotEmpty()) {
            $lines[] = "Top causes by raised:\n" . $cs->map(function($c){
                    $raised = (float)($c->donations_sum_amount ?? 0);
                    return "- #{$c->id} {$c->title} (".number_format($raised,2).")";
                })->implode("\n");
        }

        return trim(implode("\n\n", $lines));
    }
}
