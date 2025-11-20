<?php

namespace App\Core\Plugins\Contracts;

interface PluginInterface
{
    public function getName(): string;

    public function isEnabled(): bool;

    public function boot(): void;
}
