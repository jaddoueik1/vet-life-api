<?php

namespace App\Core\Plugins;

use App\Core\Plugins\Implementations\WhatsAppReminderPlugin;
use App\Core\Plugins\Implementations\ChatAppointmentPlugin;
use App\Core\Plugins\Implementations\EmployeeAccessPlugin;
use App\Core\Plugins\Implementations\LowStockAlertPlugin;
use Illuminate\Support\ServiceProvider;

class PluginRegistry extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(PluginManager::class, function ($app) {
            $manager = new PluginManager($app->make('App\\Core\\Config\\ConfigService'));
            $manager->register(new WhatsAppReminderPlugin($app->make('App\\Core\\Config\\ConfigService')));
            $manager->register(new ChatAppointmentPlugin($app->make('App\\Core\\Config\\ConfigService')));
            $manager->register(new EmployeeAccessPlugin($app->make('App\\Core\\Config\\ConfigService')));
            $manager->register(new LowStockAlertPlugin($app->make('App\\Core\\Config\\ConfigService')));
            return $manager;
        });
    }

    public function boot()
    {
        $this->app->make(PluginManager::class)->boot();
    }
}
