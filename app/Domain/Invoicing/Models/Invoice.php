<?php

namespace App\Domain\Invoicing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = ['number', 'owner_id', 'patient_id', 'visit_id', 'status', 'total'];

    public function lineItems(): HasMany
    {
        return $this->hasMany(InvoiceLineItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\Visits\Models\Visit::class);
    }
}
