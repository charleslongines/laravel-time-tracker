<?php

namespace App\Services\Admin;

use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class DashboardService
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function getDashboardData(): array
    {
        return [
            'users' => $this->getRecentUsers(),
            'stats' => $this->getDashboardStats(),
        ];
    }

    public function getRecentUsers(int $limit = 10): Collection
    {
        return $this->userRepository->getRecentNonAdminUsers($limit);
    }

    public function getDashboardStats(): array
    {
        return [
            'total_users' => $this->userRepository->countNonAdminUsers(),
        ];
    }
}
