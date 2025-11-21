<?php

namespace App\Domain\Visits\Models;

use App\Domain\Medications\Models\Medication;
use App\Domain\Services\Models\Service;
use App\Domain\Patients\Models\Patient;
use App\Domain\Users\Models\User;
use App\Domain\Inventory\Models\InventoryItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Visit extends Model
{
    protected $fillable = ['patient_id', 'vet_id', 'summary', 'diagnosis', 'treatment', 'visit_date'];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function vet(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vet_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    public function medications(): BelongsToMany
    {
        return $this->belongsToMany(Medication::class)
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class)
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function equipment(): BelongsToMany
    {
        return $this->belongsToMany(InventoryItem::class, 'equipment_visit')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
