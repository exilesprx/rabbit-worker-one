<?php

namespace App\Models;

trait Versioned
{
    protected $version;

    public function getVersion(): int
    {
        return $this->version;
    }
}