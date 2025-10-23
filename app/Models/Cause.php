<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Cause extends Model
{
    use HasFactory;

    protected $fillable = ['title','description','goal_amount','status','image_path'];

    protected $casts = [
        'goal_amount' => 'decimal:2',
    ];

    public function donations()
    {
        return $this->hasMany(Donation::class)->orderBy('date','desc');
    }

    public function totalDonations(): float
    {
        return (float) $this->donations()->sum('amount');
    }



    public function percentRaised(): int
    {
        if ($this->goal_amount <= 0) return 0;
        return (int) round(($this->totalDonations() / $this->goal_amount) * 100);
    }

    // remove file from disk when the model is deleted
    protected static function booted()
    {
        static::deleting(function (Cause $cause) {
            if ($cause->image_path && Storage::disk('public')->exists($cause->image_path)) {
                Storage::disk('public')->delete($cause->image_path);
            }
        });
    }


    /* -------- Scopes -------- */

    public function scopeSearch($q, $term)
    {
        $term = is_string($term) ? trim($term) : null;
        if ($term === null || $term === '') return $q;

        return $q->where(function ($qq) use ($term) {
            $qq->where('title', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }

    public function scopeStatus($q, $status)
    {
        if ($status === null || $status === '') return $q;
        return $q->where('status', $status);
    }

    public function scopeGoalBetween($q, $min, $max)
    {
        if ($min !== null && $min !== '') $q->where('goal_amount', '>=', (float) $min);
        if ($max !== null && $max !== '') $q->where('goal_amount', '<=', (float) $max);
        return $q;
    }

    public function scopeHasImage($q, $flag)
    {
        if ($flag === null) return $q;
        if (filter_var($flag, FILTER_VALIDATE_BOOLEAN)) {
            return $q->whereNotNull('image_path')->where('image_path', '!=', '');
        }
        return $q;
    }

    public function scopeSortBy($q, $sort)
    {
        // whitelist
        return match ($sort) {
            'title_asc'        => $q->orderBy('title'),
            'title_desc'       => $q->orderByDesc('title'),
            'goal_asc'         => $q->orderBy('goal_amount'),
            'goal_desc'        => $q->orderByDesc('goal_amount'),
            'status_asc'       => $q->orderBy('status'),
            'status_desc'      => $q->orderByDesc('status'),
            'created_desc'     => $q->orderByDesc('created_at'),
            'created_asc'      => $q->orderBy('created_at'),
            default            => $q->orderByDesc('created_at'),
        };
    }

}
