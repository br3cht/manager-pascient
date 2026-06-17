<?php

namespace App\Services;

use App\Repositories\DashboardRepository;

class DashboardService
{
    public function __construct(
        private DashboardRepository $dashboardRepository
    ) {}

    /**
     * Get dashboard totals.
     *
     * @return array<string, int>
     */
    public function totals(): array
    {
        return $this->dashboardRepository->totals();
    }
}
