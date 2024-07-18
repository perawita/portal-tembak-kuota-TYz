<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;

class Quota extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'attachment_id',
        'name',
        'price',
        'description',
        'status',
        'created_by',
        'updated_by',
    ];

    /**
     * @var array
     */
    protected $allowedFilters = [
        'id' => Where::class,
        'name' => Like::class,
    ];

    /**
     * Get the attachment record associated with the quota.
     */
    public function attachment()
    {
        return $this->belongsTo(Attachment::class);
    }

    /**
     * Get the user that created the quota.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user that updated the quota.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
