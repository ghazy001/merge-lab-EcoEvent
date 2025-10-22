<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Workshop extends Model
{
    protected $fillable = [
        'title','description','start_at','end_at','lieu_id','capacity','status'
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at'   => 'datetime',
    ];

    public function lieu() {
        return $this->belongsTo(Lieu::class, 'lieu_id');
    }

    public function materials() {
        return $this->belongsToMany(Material::class)
            ->withPivot('quantity')
            ->withTimestamps();
    }

    /* ------------ Scopes (tidy) ------------ */

    public function scopeSearch($q, $term)
    {
        $term = is_string($term) ? trim($term) : null;
        if ($term === null || $term === '') return $q;

        return $q->where(function ($qq) use ($term) {
            $qq->where('title', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }

    public function scopeStatusIs($q, $status)
    {
        if ($status === null || $status === '') return $q;
        return $q->where('status', $status); // 'draft' | 'published'
    }

    public function scopeAtLieu($q, $lieuId)
    {
        if ($lieuId === null || $lieuId === '') return $q;
        return $q->where('lieu_id', (int)$lieuId);
    }

    public function scopeStartBetween($q, $from = null, $to = null)
    {
        if ($from) $q->whereDate('start_at', '>=', $from);
        if ($to)   $q->whereDate('start_at', '<=', $to);
        return $q;
    }

    public function scopePeriod($q, $period)
    {
        switch ($period) {
            case 'upcoming': return $q->where('start_at', '>=', now());
            case 'past':     return $q->where('end_at', '<', now());
            case 'today':    return $q->whereDate('start_at', Carbon::today());
            case 'week':     return $q->whereBetween('start_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            case 'month':    return $q->whereBetween('start_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
            default:         return $q;
        }
    }

    public function scopeCapacityBetween($q, $min = null, $max = null)
    {
        if ($min !== null && $min !== '') $q->where('capacity', '>=', (int)$min);
        if ($max !== null && $max !== '') $q->where('capacity', '<=', (int)$max);
        return $q;
    }

    // Single material filter
    public function scopeHasMaterial($q, $materialId)
    {
        if ($materialId === null || $materialId === '') return $q;
        return $q->whereHas('materials', fn($m) => $m->where('materials.id', (int)$materialId));
    }

    // Multiple materials (any of)
    public function scopeHasAnyMaterials($q, $ids)
    {
        $ids = is_array($ids) ? array_filter($ids) : [];
        if (empty($ids)) return $q;
        return $q->whereHas('materials', fn($m) => $m->whereIn('materials.id', $ids));
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
            'status_asc'    => $q->orderBy('status'),
            'status_desc'   => $q->orderByDesc('status'),
            default         => $q->orderByDesc('created_at'),
        };
    }
}
