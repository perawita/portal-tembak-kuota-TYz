<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class PaymentHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'quota_id',
        'payment_url',
        'status',
        'expired_at',
        'created_at'
    ];

    /**
     * Get the quota that owns the payment history.
     */
    public function quota()
    {
        return $this->belongsTo(Quota::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
