<?php

namespace App\Core\Plugins;

use App\Core\Config\ConfigService;
use App\Core\Plugins\Contracts\PluginInterface;

abstract class BasePlugin implements PluginInterface
{
    public function __construct(protected ConfigService $config)
    {
    }

    abstract public function getName(): string;

    public function isEnabled(): bool
    {
        return (bool) $this->config->get("plugins.{$this->getName()}.enabled", false);
    }

    public function boot(): void
    {
    }
}
