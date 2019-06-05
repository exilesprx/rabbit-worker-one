<?php

namespace App\Store;

use Phalcon\Mvc\Model;

class Email extends Model
{
    protected $id;

    protected $userId;

    protected $version;

    protected $email;

    public static function fromArray(int $userId, int $version, string $email) : self
    {
        $model = new self();

        $model->userId = $userId;
        $model->version = $version;
        $model->email = $email;

        return $model;
    }

    public function onConstruct()
    {
        $this->setSource('emails');
    }

    public function columnMap()
    {
        return [
            'id' => 'id',
            'user_id' => 'userId',
            'version' => 'version',
            'email' => 'email'
        ];
    }
}