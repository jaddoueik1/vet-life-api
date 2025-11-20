<?php

namespace App\Core\Plugins;

use App\Core\Config\ConfigService;
use App\Core\Plugins\Contracts\PluginInterface;
use Illuminate\Support\Collection;

class PluginManager
{
    /** @var Collection<int, PluginInterface> */
    protected Collection $plugins;

    public function __construct(private ConfigService $configService)
    {
        $this->plugins = collect();
    }

    public function register(PluginInterface $plugin): void
    {
        $this->plugins->push($plugin);
    }

    public function boot(): void
    {
        $this->plugins->filter(fn ($plugin) => $plugin->isEnabled())
            ->each(fn ($plugin) => $plugin->boot());
    }

    public function enabled(): Collection
    {
        return $this->plugins->filter->isEnabled();
    }
}
