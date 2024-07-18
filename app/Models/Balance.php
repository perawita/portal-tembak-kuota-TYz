<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Where;

class Balance extends Model
{
    use HasFactory, Filterable;

    protected $fillable = ['user_id', 'amount', 'added_by', 'last_topup'];

    /**
     * @var array
     */
    protected $allowedFilters = [
        'id' => Where::class,
        'user_id' => Where::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Method untuk menambahkan informasi "added by"
    public function setAddedBy($userId)
    {
        $this->added_by = $userId;
    }

    // Method untuk menambahkan informasi "last top up"
    public function setLastTopUp($timestamp)
    {
        $this->last_topup = $timestamp;
    }
}
