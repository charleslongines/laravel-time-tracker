<?php

namespace App\Repositories\Contracts;

interface RepositoryInterface
{
    /**
     * Find a model by its primary key
     */
    public function find(int $id): mixed;

    /**
     * Get all models
     */
    public function all(): mixed;

    /**
     * Create a new model
     */
    public function create(array $data): mixed;

    /**
     * Update a model
     */
    public function update(mixed $model, array $data): bool;

    /**
     * Delete a model
     */
    public function delete(mixed $model): bool;
}
