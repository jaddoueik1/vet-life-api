<?php

namespace App\Domain\Invoicing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceLineItem extends Model
{
    protected $fillable = ['invoice_id', 'description', 'quantity', 'price'];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
