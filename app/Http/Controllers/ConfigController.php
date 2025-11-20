<?php

namespace App\Http\Controllers;

use App\Core\Config\ConfigService;
use Illuminate\Routing\Controller;

class ConfigController extends Controller
{
    public function show(ConfigService $config)
    {
        $configData = $config->all();
        $features = $configData['features'] ?? [];
        $allowedFeatures = collect($features)
            ->filter(fn ($enabled) => (bool) $enabled)
            ->keys()
            ->values()
            ->all();

        return [
            'deployment' => $configData['deployment'] ?? [],
            'features' => $features,
            'allowed_features' => $allowedFeatures,
            'plugins' => $configData['plugins'] ?? [],
        ];
    }
}
