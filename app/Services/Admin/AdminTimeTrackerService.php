<?php

namespace App\Services\Admin;

use App\Services\TimeTrackerService as MainTimeTrackerService;
use App\Repositories\Contracts\TimeTrackerRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminTimeTrackerService
{
    public function __construct(
        private MainTimeTrackerService $timeTrackerService,
        private TimeTrackerRepositoryInterface $timeTrackerRepository,
        private UserRepositoryInterface $userRepository
    ) {}

    public function getUserTimeTrackers(int $userId): Collection
    {
        return $this->timeTrackerService->getUserTimeTrackersOrderedByStartTime($userId);
    }

    public function getAllTimeTrackers(int $perPage = 15): LengthAwarePaginator
    {
        return $this->timeTrackerRepository->getAllWithPagination($perPage);
    }

    public function getUserTimeTrackersWithPagination(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->timeTrackerRepository->getByUserIdWithPagination($userId, $perPage);
    }

    public function getTimeTrackersByDateRange(int $userId, string $startDate, string $endDate): Collection
    {
        return $this->timeTrackerRepository->getByUserIdAndDateRange($userId, $startDate, $endDate);
    }

    public function getUserTimeTrackingStats(int $userId): array
    {
        $timeTrackers = $this->timeTrackerService->getUserTimeTrackers($userId);

        $totalSessions = $timeTrackers->count();
        $completedSessions = $timeTrackers->where('status', 'completed')->count();
        $activeSessions = $timeTrackers->where('status', 'active')->count();

        $totalDuration = 0;
        foreach ($timeTrackers->where('status', 'completed') as $tracker) {
            if ($tracker->duration) {
                $durationParts = explode(':', $tracker->duration);
                if (count($durationParts) === 3) {
                    $totalDuration += ($durationParts[0] * 3600) + ($durationParts[1] * 60) + $durationParts[2];
                }
            }
        }

        $totalHours = floor($totalDuration / 3600);
        $totalMinutes = floor(($totalDuration % 3600) / 60);

        return [
            'total_sessions' => $totalSessions,
            'completed_sessions' => $completedSessions,
            'active_sessions' => $activeSessions,
            'total_hours' => $totalHours,
            'total_minutes' => $totalMinutes,
            'total_duration_seconds' => $totalDuration,
        ];
    }

    public function getAllUsersTimeTrackingStats(): array
    {
        $users = $this->userRepository->all();
        $stats = [];

        foreach ($users as $user) {
            $stats[$user->id] = [
                'user' => $user,
                'stats' => $this->getUserTimeTrackingStats($user->id)
            ];
        }

        return $stats;
    }

    public function deleteTimeTracker(int $timeTrackerId): bool
    {
        $timeTracker = $this->timeTrackerRepository->findById($timeTrackerId);

        if (!$timeTracker) {
            return false;
        }

        return $this->timeTrackerService->deleteTimeTracker($timeTracker);
    }

    public function updateTimeTracker(int $timeTrackerId, array $data): bool
    {
        $timeTracker = $this->timeTrackerRepository->findById($timeTrackerId);

        if (!$timeTracker) {
            return false;
        }

        return $this->timeTrackerService->updateTimeTracker($timeTracker, $data);
    }
}
