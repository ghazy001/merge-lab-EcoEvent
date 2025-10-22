<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title','slug','description','status','progress','start_date','end_date'
    ];

    protected function casts(): array
    {
        return [
            'progress'   => 'integer',
            'start_date' => 'date',
            'end_date'   => 'date',
        ];
    }

    public function tasks() { return $this->hasMany(Task::class); }

    /* ------------ Tidy Scopes ------------ */

    public function scopeSearch($q, $term)
    {
        $term = is_string($term) ? trim($term) : null;
        if ($term === null || $term === '') return $q;

        return $q->where(fn($qq) =>
        $qq->where('title', 'like', "%{$term}%")
            ->orWhere('description', 'like', "%{$term}%")
        );
    }

    public function scopeStatus($q, $status)
    {
        if ($status === null || $status === '') return $q;
        // whitelist values already enforced by validation, still defensive:
        $allowed = ['planned','active','completed','archived'];
        if (!in_array($status, $allowed, true)) return $q;
        return $q->where('status', $status);
    }

    public function scopeProgressBetween($q, $min = null, $max = null)
    {
        if ($min !== null && $min !== '') $q->where('progress', '>=', (int)$min);
        if ($max !== null && $max !== '') $q->where('progress', '<=', (int)$max);
        return $q;
    }

    // Filter by start_date/end_date (YYYY-MM-DD)
    public function scopeDateBetween($q, $from = null, $to = null)
    {
        if ($from) $q->whereDate('start_date', '>=', $from);
        if ($to)   $q->whereDate('end_date',   '<=', $to);
        return $q;
    }

    public function scopeOpenTasksBetween($q, $min = null, $max = null)
    {
        // requires withCount alias open_tasks_count in the query
        if ($min !== null && $min !== '') $q->having('open_tasks_count', '>=', (int)$min);
        if ($max !== null && $max !== '') $q->having('open_tasks_count', '<=', (int)$max);
        return $q;
    }

    public function scopeSortBy($q, $sort)
    {
        // whitelist options
        return match ($sort) {
            'title_asc'         => $q->orderBy('title'),
            'title_desc'        => $q->orderByDesc('title'),
            'status_asc'        => $q->orderBy('status'),
            'status_desc'       => $q->orderByDesc('status'),
            'progress_asc'      => $q->orderBy('progress'),
            'progress_desc'     => $q->orderByDesc('progress'),
            'start_asc'         => $q->orderBy('start_date'),
            'start_desc'        => $q->orderByDesc('start_date'),
            'end_asc'           => $q->orderBy('end_date'),
            'end_desc'          => $q->orderByDesc('end_date'),
            'open_tasks_asc'    => $q->orderBy('open_tasks_count'),
            'open_tasks_desc'   => $q->orderByDesc('open_tasks_count'),
            default             => $q->orderByDesc('created_at'), // fallback
        };
    }
}
