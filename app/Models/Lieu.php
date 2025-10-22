<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lieu extends Model
{
    use HasFactory, SoftDeletes;

    // Force the French table name ("lieux")
    protected $table = 'lieux';

    protected $fillable = [
        'name',
        'address',
        'capacity',
        'description',
    ];

    // ✅ Relation to Events
    public function events()
    {
        return $this->hasMany(Event::class, 'lieu_id');
    }

    // ✅ Relation to Workshops
    public function workshops()
    {
        return $this->hasMany(Workshop::class, 'lieu_id');
    }
}
