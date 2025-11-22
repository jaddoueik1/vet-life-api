<?php

namespace App\Domain\Medications\Models;

use App\Domain\Visits\Models\Visit;
use App\Domain\Vendors\Models\Vendor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Medication extends Model
{
    protected $fillable = ['name', 'sku', 'description', 'price', 'current_stock', 'reorder_level'];

    public function visits(): BelongsToMany
    {
        return $this->belongsToMany(Visit::class)
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function vendors(): BelongsToMany
    {
        return $this->belongsToMany(Vendor::class)
            ->withPivot('is_primary')
            ->withTimestamps();
    }
}
