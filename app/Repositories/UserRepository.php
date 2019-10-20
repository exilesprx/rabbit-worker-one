<?php

namespace App\Repositories;

use App\Models\User;
use App\AggregateRoots\User as UserAR;

class UserRepository
{
    private $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function findUserById(int $userId) : UserDto
    {
        /** @var User $user */
        $user = $this->model::findFirst(
            [
                'id' => $userId
            ]
        );

        return UserDto::fromArray(
            [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'version' => $user->getVersion()
            ]
        );
    }

    public function updateEmail(UserAR $user)
    {
        $this->model->update(
            [
                'email' => $user->getEmail(),
                'version' => $user->getVersion()
            ]
        );
    }
}