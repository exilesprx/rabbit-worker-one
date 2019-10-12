<?php

namespace App\Models;

trait Timestampable
{
    public function beforeUpdate()
    {
        $date = new \DateTime();

        $this->updated_at = $date->format("Y-m-d H:i:s");
    }
}