<?php

namespace App\Services\Admin;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function createUser(array $data): User
    {
        if ($this->userRepository->findByEmail($data['email'])) {
            throw ValidationException::withMessages([
                'email' => ['A user with this email already exists.']
            ]);
        }

        $data['password'] = Hash::make($data['password']);

        $data['is_admin'] = $data['is_admin'] ?? false;

        return $this->userRepository->create($data);
    }

    public function deleteUser(User $user): bool
    {
        return $this->userRepository->delete($user);
    }

    public function getAllUsers()
    {
        return $this->userRepository->all();
    }

    public function getUserById(int $id): ?User
    {
        return $this->userRepository->findById($id);
    }

    public function getRecentNonAdminUsers(int $limit = 10)
    {
        return $this->userRepository->getRecentNonAdminUsers($limit);
    }

    public function countNonAdminUsers(): int
    {
        return $this->userRepository->countNonAdminUsers();
    }

    public function updateUser(User $user, array $data): bool
    {
        if (isset($data['email']) && $data['email'] !== $user->email) {
            if ($this->userRepository->findByEmail($data['email'])) {
                throw ValidationException::withMessages([
                    'email' => ['A user with this email already exists.']
                ]);
            }
        }

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            $data['password'] = $user->password;
        }

        return $this->userRepository->update($user, $data);
    }

    public function findUserByEmail(string $email): ?User
    {
        return $this->userRepository->findByEmail($email);
    }
}
