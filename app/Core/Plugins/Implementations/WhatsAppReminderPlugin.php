<?php

namespace App\Core\Plugins\Implementations;

use App\Core\Plugins\BasePlugin;
use App\Domain\Appointments\Events\AppointmentCreated;
use App\Domain\Visits\Events\VisitCompleted;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;

class WhatsAppReminderPlugin extends BasePlugin
{
    public function getName(): string
    {
        return 'whatsapp_reminder';
    }

    public function boot(): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        Event::listen(AppointmentCreated::class, function (AppointmentCreated $event) {
            Log::info('Scheduling WhatsApp reminder', $event->payload);
        });

        Event::listen(VisitCompleted::class, function (VisitCompleted $event) {
            Log::info('Sending post visit WhatsApp message', $event->payload);
        });
    }
}
