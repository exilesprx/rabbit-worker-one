<?php

namespace App\Repositories;

class EmailValidationDto
{
    private $id;

    private $userId;

    private $status;

    protected function __construct(int $id, int $userId, string $status)
    {
        $this->id = $id;

        $this->userId = $userId;

        $this->status = $status;
    }

    public static function fromArray(array $data) : self
    {
        return new self(
            $data['id'],
            $data['user_id'],
            $data['status']
        );
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }
}