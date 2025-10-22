<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id','title','slug','excerpt','body','image_path','is_published','published_at'
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Helper : publier maintenant si demandé
    public function publishIfNeeded(): void
    {
        if ($this->is_published && is_null($this->published_at)) {
            $this->published_at = now();
        }
    }



    /* -------- Scopes -------- */

    public function scopeSearch($q, ?string $term)
    {
        if (!filled($term)) return $q;
        return $q->where(fn($qq) =>
        $qq->where('title', 'like', "%{$term}%")
            ->orWhere('excerpt', 'like', "%{$term}%")
            ->orWhere('body', 'like', "%{$term}%")
        );
    }

    public function scopeCategoryId($q, $categoryId)
    {
        if (!filled($categoryId)) return $q;
        return $q->where('category_id', $categoryId);
    }

    public function scopeStatus($q, ?string $status)
    {
        return match ($status) {
            'published' => $q->where('is_published', true),
            'draft'     => $q->where('is_published', false),
            default     => $q
        };
    }

    public function scopePublishedBetween($q, $from = null, $to = null)
    {
        if (filled($from)) $q->whereDate('published_at', '>=', $from);
        if (filled($to))   $q->whereDate('published_at', '<=', $to);
        return $q;
    }

    public function scopeDefaultOrder($q)
    {
        return $q->orderByDesc('published_at')->orderByDesc('created_at');
    }




}

