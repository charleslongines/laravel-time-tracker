<?php

namespace App\Services;

use App\Repositories\Contracts\TimeTrackerRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class TimeTrackerService
{
    public function __construct(
        private TimeTrackerRepositoryInterface $timeTrackerRepository,
        private UserRepositoryInterface $userRepository
    ) {}

    public function getUserTimeTrackers(int $userId): Collection
    {
        return $this->timeTrackerRepository->getByUserIdOrderedByCreatedAt($userId);
    }

    public function getUserTimeTrackersOrderedByStartTime(int $userId, string $direction = 'desc'): Collection
    {
        return $this->timeTrackerRepository->getByUserIdOrderedByStartTime($userId, $direction);
    }

    public function getUserActiveTimeTracker(int $userId): mixed
    {
        return $this->timeTrackerRepository->getActiveByUserId($userId);
    }

    public function createTimeTracker(array $data): mixed
    {
        return $this->timeTrackerRepository->create($data);
    }

    public function updateTimeTracker(mixed $timeTracker, array $data): bool
    {
        return $this->timeTrackerRepository->update($timeTracker, $data);
    }

    public function deleteTimeTracker(mixed $timeTracker): bool
    {
        return $this->timeTrackerRepository->delete($timeTracker);
    }

    public function getDashboardData(int $userId): array
    {
        $timeTrackers = $this->getUserTimeTrackers($userId);
        $activeTimeTracker = $this->getUserActiveTimeTracker($userId);

        return [
            'timeTrackers' => $timeTrackers,
            'activeTimeTracker' => $activeTimeTracker,
        ];
    }

    public function clockIn(int $userId): mixed
    {
        return $this->timeTrackerRepository->create([
            'user_id' => $userId,
            'start_time' => now(),
        ]);
    }

    public function clockOut(int $userId): bool
    {
        $activeSession = $this->timeTrackerRepository->getActiveSessionByUserId($userId);

        if (!$activeSession) {
            return false;
        }

        $endTime = now();
        $startTime = $activeSession->start_time;

        if (!$startTime instanceof Carbon) {
            $startTime = Carbon::parse($startTime);
        }

        $durationInSeconds = max(0, $endTime->diffInSeconds($startTime));

        if ($durationInSeconds === 0) {
            $timestampDiff = $endTime->timestamp - $startTime->timestamp;
            $durationInSeconds = max(0, $timestampDiff);
        }

        $hours = floor($durationInSeconds / 3600);
        $minutes = floor(($durationInSeconds % 3600) / 60);
        $seconds = $durationInSeconds % 60;
        $formattedDuration = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

        return $this->timeTrackerRepository->update($activeSession, [
            'end_time' => $endTime,
            'duration' => $formattedDuration,
            'status' => 'completed',
        ]);
    }

    public function validateUser(int $userId, string $ipAddress, string $userAgent): array
    {
        $user = $this->userRepository->find($userId);

        if (!$user) {
            return [
                'valid' => false,
                'message' => 'You are not authorized to perform this action. Contact Admin to update your account.'
            ];
        }

        if ($ipAddress !== $user->ip_address) {
            return [
                'valid' => false,
                'message' => 'Your IP address: "' . $ipAddress . '" is not authorized to perform this action. Contact Admin to update your account.'
            ];
        }

        if ($userAgent !== $user->user_agent) {
            return [
                'valid' => false,
                'message' => 'Your user agent: "' . $userAgent . '" is not authorized to perform this action. Contact Admin to update your account.'
            ];
        }

        return ['valid' => true];
    }

    public function validateClockIn(int $userId, string $ipAddress, string $userAgent): array
    {
        $userValidation = $this->validateUser($userId, $ipAddress, $userAgent);
        if (!$userValidation['valid']) {
            return $userValidation;
        }

        if ($this->timeTrackerRepository->hasActiveSession($userId)) {
            return [
                'valid' => false,
                'message' => 'You already have an active time tracking session'
            ];
        }

        return ['valid' => true];
    }

    public function validateClockOut(int $userId, string $ipAddress, string $userAgent): array
    {
        $userValidation = $this->validateUser($userId, $ipAddress, $userAgent);

        if (!$userValidation['valid']) {
            return $userValidation;
        }

        if (!$this->hasActiveSession($userId)) {
            return [
                'valid' => false,
                'message' => 'You do not have an active time tracking session'
            ];
        }

        return ['valid' => true];
    }

    public function hasActiveSession(int $userId): bool
    {
        return $this->timeTrackerRepository->hasActiveSession($userId);
    }
}
