<?php

namespace App\Repositories;

use App\Models\EmailValidation;
use App\Entities\EmailValidation as EmailValidationEntity;

class EmailValidationRepository
{
    protected $model;

    public function __construct(EmailValidation $emailValidation)
    {
        $this->model = $emailValidation;
    }

    public function getByUserId(int $userId) : EmailValidationDto
    {
        /** @var EmailValidation $model */
        $model = $this->model::findFirst(
            [
                'user_id' => $userId
            ]
        );

        return EmailValidationDto::fromArray(
            [
                'id' => $model->getId(),
                'user_id' => $model->getUserId(),
                'status' => $model->getStatus()
            ]
        );
    }

    public function updateStatus(EmailValidationEntity $emailValidation)
    {
        $this->model->update(
            [
                'status' => $emailValidation->getStatus()
            ]
        );
    }
}