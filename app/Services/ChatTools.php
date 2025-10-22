<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Workshop;
use App\Models\Cause;
use App\Models\Donation;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ChatTools
{
    /**
     * Intents your backend supports (whitelist).
     */
    public static function supportedIntents(): array
    {
        return [
            'count_events_in_month',
            'list_events_between',
            'list_workshops',                 // optional filters: countryKeyword, from, to
            'count_workshops_in_country',     // countryKeyword
            'top_causes',                     // by amount raised
            'donations_stats_month',          // donations sum/count in a month
        ];
    }

    public static function execute(array $intent): array
    {
        $type = $intent['type'] ?? '';

        switch ($type) {
            case 'count_events_in_month':
                return self::countEventsInMonth(
                    (int)($intent['month'] ?? Carbon::now('Africa/Tunis')->month),
                    (int)($intent['year']  ?? Carbon::now('Africa/Tunis')->year),
                );

            case 'list_events_between':
                return self::listEventsBetween(
                    Arr::get($intent, 'date_range.from'),
                    Arr::get($intent, 'date_range.to'),
                );

            case 'list_workshops':
                return self::listWorkshops(
                    Arr::get($intent, 'countryKeyword'), // e.g. "tunisia"
                    Arr::get($intent, 'date_range.from'),
                    Arr::get($intent, 'date_range.to'),
                    Arr::get($intent, 'limit', 10),
                );

            case 'count_workshops_in_country':
                return self::countWorkshopsInCountry(
                    Arr::get($intent, 'countryKeyword', '')
                );

            case 'top_causes':
                return self::topCauses(Arr::get($intent, 'limit', 5));

            case 'donations_stats_month':
                return self::donationsStatsMonth(
                    (int)($intent['month'] ?? Carbon::now('Africa/Tunis')->month),
                    (int)($intent['year']  ?? Carbon::now('Africa/Tunis')->year),
                );

            default:
                return ['ok' => false, 'error' => 'Unsupported intent'];
        }
    }

    private static function countEventsInMonth(int $month, int $year): array
    {
        $start = Carbon::create($year, $month, 1, 0, 0, 0, 'Africa/Tunis');
        $end   = (clone $start)->endOfMonth();

        $count = Event::whereBetween('start_at', [$start, $end])->count();

        return [
            'ok' => true, 'kind' => 'count', 'payload' => [
                'text' => "We have {$count} event(s) in {$start->isoFormat('MMMM')} {$year}.",
                'count' => $count, 'month' => $month, 'year' => $year,
            ],
        ];
    }

    private static function listEventsBetween(?string $from, ?string $to): array
    {
        $from = $from ? Carbon::parse($from, 'Africa/Tunis')->startOfDay() : Carbon::now('Africa/Tunis')->startOfDay();
        $to   = $to   ? Carbon::parse($to,   'Africa/Tunis')->endOfDay()   : Carbon::now('Africa/Tunis')->addMonths(1)->endOfDay();

        $items = Event::with('lieu')
            ->whereBetween('start_at', [$from, $to])
            ->orderBy('start_at')->take(10)
            ->get(['id','title','start_at','lieu_id']);

        return [
            'ok' => true, 'kind' => 'list', 'payload' => [
                'title' => "Events from {$from->toDateString()} to {$to->toDateString()}",
                'items' => $items->map(fn($e) => [
                    'id' => $e->id,
                    'title' => $e->title,
                    'when' => optional($e->start_at)->toDayDateTimeString(),
                    'where' => $e->lieu?->name ?? 'TBA',
                ])->values(),
            ],
        ];
    }

    private static function listWorkshops(?string $countryKeyword, ?string $from, ?string $to, int $limit): array
    {
        $q = Workshop::query()->where('status', 'published');

        if ($countryKeyword) {
            $kw = strtolower($countryKeyword);
            $q->whereHas('lieu', function ($qq) use ($kw) {
                $qq->whereRaw('LOWER(address) LIKE ?', ["%{$kw}%"])
                    ->orWhereRaw('LOWER(name) LIKE ?',    ["%{$kw}%"]);
            });
        }

        if ($from || $to) {
            $from = $from ? Carbon::parse($from, 'Africa/Tunis')->startOfDay() : Carbon::now('Africa/Tunis')->startOfDay();
            $to   = $to   ? Carbon::parse($to,   'Africa/Tunis')->endOfDay()   : Carbon::now('Africa/Tunis')->addMonths(6)->endOfDay();
            $q->whereBetween('start_at', [$from, $to]);
        }

        $items = $q->with('lieu')->orderBy('start_at')->take($limit)->get(['id','title','start_at','capacity','lieu_id']);

        return [
            'ok' => true, 'kind' => 'list', 'payload' => [
                'title' => 'Workshops' . ($countryKeyword ? ' in '.ucfirst($countryKeyword) : ''),
                'items' => $items->map(fn($w) => [
                    'id' => $w->id,
                    'title' => $w->title,
                    'when' => optional($w->start_at)->toDayDateTimeString(),
                    'where' => $w->lieu?->name ?? 'TBA',
                    'capacity' => $w->capacity,
                ])->values(),
            ],
        ];
    }

    private static function countWorkshopsInCountry(string $countryKeyword): array
    {
        $kw = strtolower($countryKeyword);
        $count = Workshop::where('status','published')
            ->whereHas('lieu', fn($q) =>
            $q->whereRaw('LOWER(address) LIKE ?', ["%{$kw}%"])
                ->orWhereRaw('LOWER(name) LIKE ?',    ["%{$kw}%"])
            )->count();

        return [
            'ok' => true, 'kind' => 'count', 'payload' => [
                'text' => "There are {$count} published workshop(s) in ".ucfirst($countryKeyword).".",
                'count' => $count, 'countryKeyword' => $countryKeyword,
            ],
        ];
    }

    private static function topCauses(int $limit): array
    {
        $causes = Cause::withSum('donations','amount')
            ->orderByDesc('donations_sum_amount')
            ->take($limit)
            ->get(['id','title','goal_amount']);

        return [
            'ok' => true, 'kind' => 'list', 'payload' => [
                'title' => "Top {$limit} causes by amount raised",
                'items' => $causes->map(function ($c) {
                    $raised = (float)($c->donations_sum_amount ?? 0);
                    return [
                        'id' => $c->id,
                        'title' => $c->title,
                        'raised' => number_format($raised, 2),
                        'goal' => number_format((float)$c->goal_amount, 2),
                        'progress' => ($c->goal_amount > 0) ? round(($raised / $c->goal_amount) * 100) : 0,
                    ];
                })->values(),
            ],
        ];
    }

    private static function donationsStatsMonth(int $month, int $year): array
    {
        $start = Carbon::create($year, $month, 1, 0, 0, 0, 'Africa/Tunis');
        $end   = (clone $start)->endOfMonth();

        $sum   = (float) Donation::whereBetween('date', [$start, $end])->sum('amount');
        $count = (int)   Donation::whereBetween('date', [$start, $end])->count();

        return [
            'ok' => true, 'kind' => 'count', 'payload' => [
                'text' => "Donations in {$start->isoFormat('MMMM')} {$year}: {$count} donations totaling ".number_format($sum, 2),
                'count' => $count, 'sum' => $sum,
            ],
        ];
    }
}
