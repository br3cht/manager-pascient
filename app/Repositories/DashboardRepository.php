<?php

namespace App\Repositories;

use App\Models\Address;
use App\Models\Patient;

class DashboardRepository
{
    /**
     * Get dashboard totals.
     *
     * @return array<string, int>
     */
    public function totals(): array
    {
        return [
            'patients_total' => Patient::query()->count(),
            'addresses_total' => Address::query()->count(),
        ];
    }
}
