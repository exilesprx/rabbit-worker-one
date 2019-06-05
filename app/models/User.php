<?php

namespace App\models;

use Phalcon\Mvc\Model;

class User extends Model
{
    protected $id;

    protected $version;

    protected $email;

    public static function with(string $email, int $version) : self
    {
        $model = new self();

        $model->email = $email;

        $model->version = $version;

        return $model;
    }

    public function onConstruct()
    {
        $this->setSource('users');
    }
}