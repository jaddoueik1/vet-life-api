<?php

namespace App\Providers;

use App\Domain\Visits\Events\VisitCreated;
use App\Domain\Visits\Listeners\CreateInvoiceForVisit;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        VisitCreated::class => [
            CreateInvoiceForVisit::class,
        ],
    ];
}
