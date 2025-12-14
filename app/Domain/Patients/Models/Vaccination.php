<?php

namespace App\Domain\Patients\Models;

use App\Domain\Visits\Models\Visit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Vaccination extends Model
{
    protected $fillable = ['name', 'description'];

    public function healthPlans(): BelongsToMany
    {
        return $this->belongsToMany(HealthPlan::class)
            ->withPivot(['frequency_days', 'start_age_weeks']);
    }

    public function visits(): BelongsToMany
    {
        return $this->belongsToMany(Visit::class, 'vaccination_visit');
    }
}
