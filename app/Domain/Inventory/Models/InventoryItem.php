<?php

namespace App\Domain\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Domain\Visits\Models\Visit;
use App\Domain\Vendors\Models\Vendor;

class InventoryItem extends Model
{
    protected $fillable = ['name', 'sku', 'reorder_level', 'quantity', 'price'];

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

    public function vendors(): BelongsToMany
    {
        return $this->belongsToMany(Vendor::class)
            ->withPivot('is_primary')
    }
    
    public function appointmentTemplates(): BelongsToMany
    {
        return $this->belongsToMany(\App\Domain\Appointments\Models\AppointmentTemplate::class, 'appointment_template_inventory_item')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
