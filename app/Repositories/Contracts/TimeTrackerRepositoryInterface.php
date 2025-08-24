<?php

namespace App\Repositories\Contracts;

use App\Models\TimeTracker;
use Illuminate\Database\Eloquent\Collection;

interface TimeTrackerRepositoryInterface extends RepositoryInterface
{
    public function getByUserId(int $userId): Collection;

    public function getByUserIdOrderedByCreatedAt(int $userId, string $direction = 'desc'): Collection;

    public function getByUserIdOrderedByStartTime(int $userId, string $direction = 'desc'): Collection;

    public function getActiveByUserId(int $userId): ?TimeTracker;

    public function getActiveSessionByUserId(int $userId): ?TimeTracker;

    public function hasActiveSession(int $userId): bool;

    public function getAllWithPagination(int $perPage = 15): \Illuminate\Pagination\LengthAwarePaginator;

    public function getByUserIdWithPagination(int $userId, int $perPage = 15): \Illuminate\Pagination\LengthAwarePaginator;

    public function getByUserIdAndDateRange(int $userId, string $startDate, string $endDate): Collection;
}
