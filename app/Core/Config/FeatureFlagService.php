<?php

namespace App\Core\Config;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FeatureFlagService
{
    public function __construct(private ConfigService $configService)
    {
    }

    public function isEnabled(string $feature): bool
    {
        return (bool) $this->configService->get("features.$feature", false);
    }

    public function ensureEnabled(string $feature): void
    {
        if (!$this->isEnabled($feature)) {
            throw new NotFoundHttpException("Feature {$feature} is disabled.");
        }
    }
}
