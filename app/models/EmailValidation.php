<?php

namespace App\Models;

use App\StateMachines\EmailValidationState;
use App\StateMachines\InvalidEmail;
use App\StateMachines\NewEmail;
use App\StateMachines\ValidEmail;

class EmailValidation extends BaseModel
{
    protected $id;

    protected $userId;

    protected $status;

    public function columnMap()
    {
        return [
            'id' => 'id',
            'user_id' => 'userId',
            'status' => 'status',
            'created_at' => 'createdAt',
            'updated_at' => 'updatedAt'
        ];
    }

    /**
     * @return mixed
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUserId() : int
    {
        return $this->userId;
    }

    public function getStatus() : EmailValidationState
    {
        if (NewEmail::equals($this->status)) {
            return new NewEmail();
        } else if(InvalidEmail::equals($this->status)) {
            return new InvalidEmail();
        }

        return new ValidEmail();
    }

    public static function findByUserId(int $id) : self
    {
        return self::findFirst("userId = {$id}");
    }
}