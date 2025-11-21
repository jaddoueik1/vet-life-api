<?php

namespace App\Domain\Services\Models;

use App\Domain\Visits\Models\Visit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Service extends Model
{
    protected $fillable = ['name', 'description', 'price'];

    public function visits(): BelongsToMany
    {
        return $this->belongsToMany(Visit::class)
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
