<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function getRecentNonAdminUsers(int $limit = 10): Collection;

    public function countNonAdminUsers(): int;

    public function findById(int $id): ?User;

    public function findByEmail(string $email): ?User;
}
