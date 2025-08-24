<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\TimeTrackerService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TimeTrackerController extends Controller
{
    public function __construct(
        private TimeTrackerService $timeTrackerService
    ) {}

    public function clockIn(Request $request)
    {
        $validation = $this->timeTrackerService->validateClockIn(
            $request->user()->id,
            $request->ipAddress,
            $request->userAgent
        );

        if (!$validation['valid']) {
            return back()->withErrors(['message' => $validation['message']]);
        }

        $this->timeTrackerService->clockIn($request->user()->id);
        return redirect()->route('dashboard')->with('success', 'Successfully clocked in');
    }

    public function clockOut(Request $request)
    {
        $validation = $this->timeTrackerService->validateClockOut(
            $request->user()->id,
            $request->ipAddress,
            $request->userAgent
        );

        if (!$validation['valid']) {
            return back()->withErrors(['message' => $validation['message']]);
        }

        $this->timeTrackerService->clockOut($request->user()->id);
        return redirect()->route('dashboard')->with('success', 'Successfully clocked out');
    }
}
