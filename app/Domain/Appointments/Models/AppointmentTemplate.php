<?php

namespace App\Domain\Appointments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Domain\Inventory\Models\InventoryItem;
use App\Domain\Users\Models\User;

class AppointmentTemplate extends Model
{
    protected $fillable = ['name', 'duration', 'description'];

    public function inventoryItems(): BelongsToMany
    {
        return $this->belongsToMany(InventoryItem::class, 'appointment_template_inventory_item')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function vets(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'appointment_template_user')
            ->withTimestamps();
    }
}
