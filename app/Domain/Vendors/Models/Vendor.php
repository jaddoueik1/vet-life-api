<?php

namespace App\Domain\Vendors\Models;

use App\Domain\Inventory\Models\InventoryItem;
use App\Domain\Medications\Models\Medication;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Vendor extends Model
{
    protected $fillable = [
        'name',
        'main_contact_name',
        'main_contact_email',
        'main_contact_phone',
        'secondary_contact_name',
        'secondary_contact_email',
        'secondary_contact_phone',
    ];

    public function medications(): BelongsToMany
    {
        return $this->belongsToMany(Medication::class)
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    public function inventoryItems(): BelongsToMany
    {
        return $this->belongsToMany(InventoryItem::class)
            ->withPivot('is_primary')
            ->withTimestamps();
    }

}
