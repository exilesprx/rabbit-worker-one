<?php

namespace App\Repositories;

use App\AggregateRoots\User;
use App\Entities\EmailValidation;

class UserService implements UserApplicationLayerContract
{
    protected $userRepo;

    protected $emailRepo;

    public function __construct(UserRepository $userRepo, EmailValidationRepository $emailRepo)
    {
        $this->userRepo = $userRepo;

        $this->emailRepo = $emailRepo;
    }

    public function findUserById(int $userId) : \App\AggregateRoots\UserApplicationLayerContract
    {
        $email = $this->emailRepo->getByUserId($userId);
        $user = $this->userRepo->findUserById($userId);

        $validation = new EmailValidation(
            $email->getId(),
            $email->getUserId(),
            $email->getStatus()
        );

        return new User(
            $user->getId(),
            $user->getEmail(),
            $user->getVersion(),
            $validation
        );
    }
}