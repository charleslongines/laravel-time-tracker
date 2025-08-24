<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Admin\UserService;
use App\Services\Admin\AdminTimeTrackerService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class UserController extends Controller
{
    public function __construct(
        private UserService $userService,
        private AdminTimeTrackerService $adminTimeTrackerService
    ) {}

    public function index()
    {
        return redirect()->route('admin.dashboard');
    }

    public function create()
    {
        return Inertia::render('Admin/Users/Create');
    }

    public function edit(User $user)
    {
        return Inertia::render('Admin/Users/Edit', [
            'user' => $user
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'is_admin' => 'boolean',
        ]);

        $this->userService->createUser($request->all());

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'is_admin' => 'boolean',
        ]);

        $this->userService->updateUser($user, $request->all());

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $this->userService->deleteUser($user);

        return redirect()->route('admin.dashboard')
            ->with('success', 'User deleted successfully.');
    }

    public function timeTracker(User $user)
    {
        $timeTrackers = $this->adminTimeTrackerService->getUserTimeTrackers($user->id);
        $stats = $this->adminTimeTrackerService->getUserTimeTrackingStats($user->id);

        return Inertia::render('Admin/Users/TimeTracker', [
            'user' => $user,
            'timeTrackers' => $timeTrackers,
            'stats' => $stats
        ]);
    }
}
