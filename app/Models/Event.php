<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title','description','start_at','end_at','lieu_id','capacity',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at'   => 'datetime',
    ];

    public function lieu()
    {
        return $this->belongsTo(Lieu::class, 'lieu_id');
    }

    /* ------------ Scopes ------------ */

    public function scopeSearch($q, $term)
    {
        $term = is_string($term) ? trim($term) : null;
        if ($term === null || $term === '') return $q;

        return $q->where(function ($qq) use ($term) {
            $qq->where('title', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }

    public function scopeAtLieu($q, $lieuId)
    {
        if ($lieuId === null || $lieuId === '') return $q;
        return $q->where('lieu_id', (int)$lieuId);
    }

    // Filter by start_at between two dates (YYYY-MM-DD)
    public function scopeStartBetween($q, $from = null, $to = null)
    {
        if ($from) $q->whereDate('start_at', '>=', $from);
        if ($to)   $q->whereDate('start_at', '<=', $to);
        return $q;
    }

    public function scopeCapacityBetween($q, $min = null, $max = null)
    {
        if ($min !== null && $min !== '') $q->where('capacity', '>=', (int)$min);
        if ($max !== null && $max !== '') $q->where('capacity', '<=', (int)$max);
        return $q;
    }

    // Handy "period" buckets for quick filtering
    public function scopePeriod($q, $period)
    {
        switch ($period) {
            case 'upcoming':
                return $q->where('start_at', '>=', now());
            case 'past':
                return $q->where('end_at', '<', now());
            case 'today':
                return $q->whereDate('start_at', Carbon::today());
            case 'week':
                return $q->whereBetween('start_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            case 'month':
                return $q->whereBetween('start_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
            default:
                return $q;
        }
    }

    public function scopeSortBy($q, $sort)
    {
        return match ($sort) {
            'start_asc'     => $q->orderBy('start_at'),
            'start_desc'    => $q->orderByDesc('start_at'),
            'end_asc'       => $q->orderBy('end_at'),
            'end_desc'      => $q->orderByDesc('end_at'),
            'title_asc'     => $q->orderBy('title'),
            'title_desc'    => $q->orderByDesc('title'),
            'capacity_asc'  => $q->orderBy('capacity'),
            'capacity_desc' => $q->orderByDesc('capacity'),
            default         => $q->orderByDesc('start_at'), // sensible default
        };
    }
}
