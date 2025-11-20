<?php

namespace App\Domain\Patients\Models;

use App\Domain\Visits\Models\Visit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    protected $fillable = ['owner_id', 'name', 'species', 'breed', 'age', 'sex'];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }
}
