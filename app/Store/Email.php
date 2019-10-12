<?php

namespace App\Store;

use App\Models\BaseModel;
use App\Models\Versioned;
use Ramsey\Uuid\UuidInterface;

class Email extends BaseModel
{
    use Versioned;

    protected $id;

    protected $uuid;

    protected $userId;

    protected $email;

    protected $createdAt;

    public static function with(UuidInterface $uuid, int $userId, int $version, string $email): self
    {
        $model = new self();
        $model->uuid = $uuid;
        $model->userId = $userId;
        $model->version = $version;
        $model->email = $email;
        $model->createdAt = (new \DateTime())->format("Y-m-d H:i:s");

        return $model;
    }

    public function columnMap()
    {
        return [
            'id' => 'id',
            'uuid' => 'uuid',
            'user_id' => 'userId',
            'version' => 'version',
            'email' => 'email',
            'created_at' => 'createdAt'
        ];
    }
}