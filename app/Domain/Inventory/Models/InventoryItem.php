<?php

namespace App\Domain\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    protected $fillable = ['name', 'sku', 'reorder_level'];

    public function batches(): HasMany
    {
        return $this->hasMany(StockBatch::class);
    }
}
