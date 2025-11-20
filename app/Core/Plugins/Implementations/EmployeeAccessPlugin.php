<?php

namespace App\Core\Plugins\Implementations;

use App\Core\Plugins\BasePlugin;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class EmployeeAccessPlugin extends BasePlugin
{
    public function getName(): string
    {
        return 'employee_access';
    }

    public function boot(): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        $rules = $this->config->get('plugins.employee_access.rules', []);
        foreach ($rules as $role => $permissions) {
            foreach ($permissions as $ability => $value) {
                Gate::define("{$role}.{$ability}", fn ($user) => $user->hasRole($role) && (bool) $value);
            }
        }

        Log::info('Employee access policies registered');
    }
}
