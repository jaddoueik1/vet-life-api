<?php

namespace App\Domain\Medications\Models;

use App\Domain\Visits\Models\Visit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Medication extends Model
{
    protected $fillable = [
        'name',
        'sku',
        'strength',
        'category',
        'dosage',
        'price',
        'current_stock',
        'reorder_level',
    ];

    public function visits(): BelongsToMany
    {
        return $this->belongsToMany(Visit::class)
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
