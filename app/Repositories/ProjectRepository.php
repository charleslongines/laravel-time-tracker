<?php

namespace App\Repositories;

use App\Models\Project;
use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ProjectRepository implements RepositoryInterface
{
    public function __construct(
        private Project $project
    ) {}
    public function find(int $id): mixed
    {
        return $this->project->find($id);
    }

    public function all(): mixed
    {
        return $this->project->all();
    }

    public function create(array $data): mixed
    {
        return $this->project->create($data);
    }

    public function update(mixed $model, array $data): bool
    {
        return $model->update($data);
    }

    public function delete(mixed $model): bool
    {
        return $model->delete();
    }

    public function getByUserId(int $userId): Collection
    {
        return $this->project
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function count(): int
    {
        return $this->project->count();
    }
}
