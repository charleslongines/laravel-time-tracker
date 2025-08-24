<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\TimeTrackerService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct(
        private TimeTrackerService $timeTrackerService
    ) {}

    public function index()
    {
        $dashboardData = $this->timeTrackerService->getDashboardData(auth()->user()->id);

        return Inertia::render('User/Dashboard', $dashboardData);
    }
}
