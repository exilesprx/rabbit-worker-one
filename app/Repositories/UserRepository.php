<?php

namespace App\Repositories;

use App\AggregateRoots\User;
use App\Entities\EmailValidation;
use App\Models\EmailValidation as EmailValidationModel;
use App\Models\User as UserModel;
use App\AggregateRoots\User as UserAggregateRoot;
use App\StateMachines\EmailValidationState;

class UserRepository
{
    private $user;

    private $emailValidation;

    public function __construct(UserModel $user, EmailValidationModel $emailValidation)
    {
        $this->user = $user;

        $this->emailValidation = $emailValidation;
    }

    public function findUserById(int $userId) : UserAggregateRoot
    {
        $user = $this->user::findFirst(
            [
                'id' => $userId
            ]
        );

        $validation = $this->findEmailValidationByUserId($userId);

        return new User(
            $user->getId(),
            $user->getEmail(),
            $user->getVersion(),
            $validation
        );
    }

    private function findEmailValidationByUserId(int $userId) : EmailValidation
    {
        $model = $this->emailValidation::findFirst(
            [
                'user_id' => $userId
            ]
        );

        return new EmailValidation(
            $model->getId(),
            $model->getUserId(),
            $model->getStatus()
        );
    }

    public function updateEmail(UserAggregateRoot $user)
    {
        $userModel = $this->user::findById($user->getId());

        $userModel->update(
            [
                'email' => $user->getEmail(),
                'version' => $user->getVersion()
            ]
        );

        $this->updateEmailValidationStatus($user->getId(), $user->getEmailStatus());
    }

    private function updateEmailValidationStatus(int $userId, EmailValidationState $state)
    {
        $emailModel = $this->emailValidation::findByUserId($userId);

        $emailModel->update(
            [
                'status' => (string)$state
            ]
        );
    }
}