<?php

namespace App\Http\Middleware;

use App\Core\Config\FeatureFlagService;
use Closure;
use Illuminate\Http\Request;

class EnsureFeatureEnabled
{
    public function __construct(private FeatureFlagService $featureFlags)
    {
    }

    public function handle(Request $request, Closure $next, string $feature)
    {
        $this->featureFlags->ensureEnabled($feature);
        return $next($request);
    }
}
