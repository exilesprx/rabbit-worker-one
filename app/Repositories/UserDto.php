<?php

namespace App\Repositories;

class UserDto
{
    private $id;

    private $email;

    private $version;

    protected function __construct(int $id, string $email, int $version)
    {
        $this->id = $id;

        $this->email = $email;

        $this->version = $version;
    }

    public static function fromArray(array $data) : self
    {
        return new self($data['id'], $data['version'], $data['version']);
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