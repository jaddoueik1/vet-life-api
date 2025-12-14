<?php

namespace App\Domain\Patients\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HealthPlan extends Model
{
    protected $fillable = ['species_id', 'name', 'description'];

    public function species(): BelongsTo
    {
        return $this->belongsTo(Species::class);
    }

    public function breeds(): HasMany
    {
        return $this->hasMany(Breed::class);
    }

    public function vaccinations(): BelongsToMany
    {
        return $this->belongsToMany(Vaccination::class)
            ->withPivot(['frequency_days', 'start_age_weeks']);
    }
}
