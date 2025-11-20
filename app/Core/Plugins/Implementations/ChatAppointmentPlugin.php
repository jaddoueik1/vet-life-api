<?php

namespace App\Core\Plugins\Implementations;

use App\Core\Plugins\BasePlugin;
use Illuminate\Support\Facades\Log;

class ChatAppointmentPlugin extends BasePlugin
{
    public function getName(): string
    {
        return 'chat_bot';
    }

    public function boot(): void
    {
        if ($this->isEnabled()) {
            Log::info('Chat bot plugin ready');
        }
    }
}
