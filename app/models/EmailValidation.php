<?php

namespace App\Models;

class EmailValidation extends BaseModel
{
    protected $id;

    protected $userId;

    protected $status;

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

    /**
     * @return mixed
     */
    public function getStatus() : string
    {
        return $this->status;
    }
}