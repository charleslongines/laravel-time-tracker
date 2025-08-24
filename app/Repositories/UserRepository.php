<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(
        private User $user
    ) {}

    public function getRecentNonAdminUsers(int $limit = 10): Collection
    {
        return $this->user
            ->select('id', 'name', 'email', 'is_admin', 'created_at', 'ip_address', 'user_agent')
            ->where('is_admin', false)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function countNonAdminUsers(): int
    {
        return $this->user
            ->where('is_admin', false)
            ->count();
    }

    public function find(int $id): mixed
    {
        return $this->user->find($id);
    }

    public function all(): mixed
    {
        return $this->user->all();
    }

    public function findById(int $id): ?User
    {
        return $this->user->find($id);
    }

    public function create(array $data): User
    {
        return $this->user->create($data);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->user->where('email', $email)->first();
    }

    public function update(mixed $model, array $data): bool
    {
        return $model->update($data);
    }

    public function delete(mixed $model): bool
    {
        return $model->delete();
    }
}
