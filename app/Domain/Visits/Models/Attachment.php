<?php

namespace App\Domain\Visits\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attachment extends Model
{
    protected $fillable = ['visit_id', 'path', 'label'];

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }
}
