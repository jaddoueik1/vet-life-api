<?php

namespace App\Core\Config;

use Illuminate\Support\Facades\File;
use Symfony\Component\Yaml\Yaml;

class ConfigService
{
    protected array $config;

    public function __construct(string $path = null)
    {
        $path = $path ?? base_path('deployment-config.yml');
        $this->config = File::exists($path) ? Yaml::parseFile($path) : [];
    }

    public function get(string $key, $default = null)
    {
        return data_get($this->config, $key, $default);
    }

    public function all(): array
    {
        return $this->config;
    }
}
