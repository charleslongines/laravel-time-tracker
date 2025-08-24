<?php

namespace App\Repositories;

use App\Models\TimeTracker;
use App\Repositories\Contracts\TimeTrackerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TimeTrackerRepository implements TimeTrackerRepositoryInterface
{
    public function __construct(
        private TimeTracker $timeTracker
    ) {}

    public function getByUserId(int $userId): Collection
    {
        return $this->timeTracker
            ->where('user_id', $userId)
            ->get();
    }

    public function getByUserIdOrderedByCreatedAt(int $userId, string $direction = 'desc'): Collection
    {
        return $this->timeTracker
            ->where('user_id', $userId)
            ->orderBy('created_at', $direction)
            ->get();
    }

    public function getByUserIdOrderedByStartTime(int $userId, string $direction = 'desc'): Collection
    {
        return $this->timeTracker
            ->where('user_id', $userId)
            ->orderBy('start_time', $direction)
            ->get();
    }

    public function getActiveByUserId(int $userId): ?TimeTracker
    {
        return $this->timeTracker
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->first();
    }

    public function getActiveSessionByUserId(int $userId): ?TimeTracker
    {
        return $this->timeTracker
            ->where('user_id', $userId)
            ->whereNull('end_time')
            ->first();
    }

    public function hasActiveSession(int $userId): bool
    {
        return $this->timeTracker
            ->where('user_id', $userId)
            ->whereNull('end_time')
            ->exists();
    }

    public function find(int $id): mixed
    {
        return $this->timeTracker->find($id);
    }

    public function all(): mixed
    {
        return $this->timeTracker->all();
    }

    public function create(array $data): mixed
    {
        return $this->timeTracker->create($data);
    }

    public function update(mixed $model, array $data): bool
    {
        return $model->update($data);
    }

    public function delete(mixed $model): bool
    {
        return $model->delete();
    }

    public function getAllWithPagination(int $perPage = 15): \Illuminate\Pagination\LengthAwarePaginator
    {
        return $this->timeTracker->paginate($perPage);
    }

    public function getByUserIdWithPagination(int $userId, int $perPage = 15): \Illuminate\Pagination\LengthAwarePaginator
    {
        return $this->timeTracker
            ->where('user_id', $userId)
            ->orderBy('start_time', 'desc')
            ->paginate($perPage);
    }

    public function getByUserIdAndDateRange(int $userId, string $startDate, string $endDate): Collection
    {
        return $this->timeTracker
            ->where('user_id', $userId)
            ->whereBetween('start_time', [$startDate, $endDate])
            ->orderBy('start_time', 'desc')
            ->get();
    }
}
