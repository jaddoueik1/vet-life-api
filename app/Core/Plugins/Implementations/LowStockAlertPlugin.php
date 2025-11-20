<?php

namespace App\Core\Plugins\Implementations;

use App\Core\Plugins\BasePlugin;
use App\Domain\Inventory\Events\StockLow;
use App\Domain\Inventory\Events\StockExpired;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class LowStockAlertPlugin extends BasePlugin
{
    public function getName(): string
    {
        return 'low_stock_alert';
    }

    public function boot(): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        Event::listen(StockLow::class, fn (StockLow $event) => Log::warning('Stock low alert', $event->payload));
        Event::listen(StockExpired::class, fn (StockExpired $event) => Log::warning('Stock expired alert', $event->payload));
    }
}
