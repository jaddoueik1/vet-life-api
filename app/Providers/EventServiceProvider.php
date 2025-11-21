<?php

namespace App\Providers;

use App\Domain\Inventory\Events\MedicationUsed;
use App\Domain\Inventory\Listeners\DeductMedicationStock;
use App\Domain\Inventory\Events\EquipmentUsed;
use App\Domain\Inventory\Listeners\DeductEquipmentStock;
use App\Domain\Visits\Events\VisitCreated;
use App\Domain\Visits\Listeners\CreateInvoiceForVisit;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        VisitCreated::class => [
            CreateInvoiceForVisit::class,
        ],
        MedicationUsed::class => [
            DeductMedicationStock::class,
        ],
        EquipmentUsed::class => [
            DeductEquipmentStock::class,
        ],
    ];
}
