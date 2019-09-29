<?php

namespace App\models;

trait Versioned
{
    protected $version;

    public function getVersion(): int
    {
        return $this->version;
    }
}