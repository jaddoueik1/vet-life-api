<?php

namespace App\Domain\Visits\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domain\Patients\Models\Patient;
use App\Domain\Users\Models\User;

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
}
