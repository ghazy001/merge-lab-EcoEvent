<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $fillable = ['cause_id','donor_name','amount','date', 'message', 'checkout_session_id'];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    public function cause()
    {
        return $this->belongsTo(Cause::class);
    }
}
