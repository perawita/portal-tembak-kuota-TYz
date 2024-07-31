<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachmentable extends Model
{
    use HasFactory;

    protected $fillable = [
        'attachmentable_type',
        'attachmentable_id',
        'attachment_id',
    ];

    /**
     * Get the owning attachmentable model.
     */
    public function attachmentable()
    {
        return $this->morphTo();
    }

    /**
     * Get the attachment record associated with the attachmentable.
     */
    public function attachment()
    {
        return $this->belongsTo(Attachment::class);
    }
}
