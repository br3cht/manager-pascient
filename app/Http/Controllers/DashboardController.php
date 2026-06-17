<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(
        public readonly DashboardService $dashboardService
    ) {}

    public function index()
    {
        return response()->json([
            'data' => $this->dashboardService->totals(),
        ]);
    }
}
