<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = ['name','stock','unit'];

    public function workshops() {
        return $this->belongsToMany(Workshop::class)
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
