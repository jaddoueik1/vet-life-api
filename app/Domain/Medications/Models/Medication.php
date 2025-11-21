<?php

namespace App\Domain\Medications\Models;

use App\Domain\Visits\Models\Visit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Medication extends Model
{
    protected $fillable = ['name', 'description', 'unit_price'];

    public function visits(): BelongsToMany
    {
        return $this->belongsToMany(Visit::class)
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
