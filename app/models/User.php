<?php

namespace App\Models;

class User extends BaseModel
{
    use Timestampable, Versioned;

    protected $id;

    protected $email;

    protected $version;

    public static function with(string $email, int $version) : self
    {
        $model = new self();

        $model->email = $email;

        $model->version = $version;

        return $model;
    }

    public function getId() : int
    {
        return $this->id;
    }

    public function getEmail() : string
    {
        return $this->email;
    }

    public function getVersion() : int
    {
        return $this->version;
    }
}