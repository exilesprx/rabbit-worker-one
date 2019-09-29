<?php

namespace App\models;

class User extends BaseModel
{
    use Timestampable, Versioned;

    protected $id;

    protected $email;

    public static function with(string $email, int $version) : self
    {
        $model = new self();

        $model->email = $email;

        $model->version = $version;

        return $model;
    }
}