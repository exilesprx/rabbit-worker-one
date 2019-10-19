<?php

namespace App\Tasks;

use App\Exceptions\OutOfOrderException;

interface TaskContract
{
    /**
     * @param string $name
     * @param array $data
     *
     */
    public function execute(string $name, array $data);
}