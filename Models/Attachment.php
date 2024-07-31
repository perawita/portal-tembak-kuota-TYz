<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;

class Attachment extends Model
{
    use HasFactory, Filterable;

    protected $fillable = [
        'name',
        'original_name',
        'mime',
        'extension',
        'size',
        'sort',
        'path',
        'description',
        'alt',
        'hash',
        'disk',
        'user_id',
        'group',
    ];


    /**
     * @var array
     */
    protected $allowedFilters = [
        'id' => Where::class,
        'name' => Like::class,
        'original_name' => Like::class,
    ];

    /**
     * The attributes for which can use sort in url.
     *
     * @var array
     */
    protected $allowedSorts = [
        'name',
        'original_name',
        'mime',
        'extension',
        'size',
        'sort',
        'path',
        'description',
        'alt',
        'hash',
        'disk',
        'user_id',
        'group',
    ];


    /**
     * Get the user that owns the attachment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all of the models that own attachments.
     */
    public function attachmentable()
    {
        return $this->morphTo();
    }
}
