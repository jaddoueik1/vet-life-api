<?php

namespace App\Domain\Patients\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Patient extends Model
{
    protected $fillable = ['owner_id', 'name', 'species', 'breed', 'age', 'sex'];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }
}
