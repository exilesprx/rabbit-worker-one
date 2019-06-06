<?php

namespace App\Store;

use App\models\BaseModel;
use Ramsey\Uuid\UuidInterface;

class Email extends BaseModel
{
    protected $id;

    protected $uuid;

    protected $userId;

    protected $version;

    protected $email;

    public static function with(UuidInterface $uuid, int $userId, int $version, string $email): self
    {
        $model = new self();
        $model->uuid = $uuid;
        $model->userId = $userId;
        $model->version = $version;
        $model->email = $email;

        return $model;
    }

    public function columnMap()
    {
        return [
            'id' => 'id',
            'uuid' => 'uuid',
            'user_id' => 'userId',
            'version' => 'version',
            'email' => 'email'
        ];
    }
}