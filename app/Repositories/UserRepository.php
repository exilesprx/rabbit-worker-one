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

    public function findUserById(UserDto $user) : UserAR
    {
        /** @var User $user */
        $user = $this->model::findFirst(
            [
                'id' => $user->getId()
            ]
        );

        return new UserAR(
            $user->getId(),
            $user->getEmail(),
            $user->getVersion()
        );
    }

    public function updateUser(UserAR $user)
    {
        $this->model->update(
            [
                'email' => $user->getEmail(),
                'version' => $user->getVersion()
            ]
        );
    }
}