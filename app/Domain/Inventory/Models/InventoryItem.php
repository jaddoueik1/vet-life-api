<?php

namespace App\Domain\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Domain\Visits\Models\Visit;

class InventoryItem extends Model
{
    protected $fillable = ['name', 'sku', 'reorder_level'];

    public function batches(): HasMany
    {
        return $this->hasMany(StockBatch::class);
    }

    public function visits(): BelongsToMany
    {
        return $this->belongsToMany(Visit::class, 'inventory_item_visit')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
