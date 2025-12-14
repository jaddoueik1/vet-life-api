<?php

namespace App\Domain\Patients\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Species extends Model
{
    protected $fillable = ['name'];

    public function breeds(): HasMany
    {
        return $this->hasMany(Breed::class);
    }

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }

    public function healthPlans(): HasMany
    {
        return $this->hasMany(HealthPlan::class);
    }
}
