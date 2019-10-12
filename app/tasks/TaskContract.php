<?php

namespace App\Tasks;

use App\Exceptions\OutOfOrderException;

interface TaskContract
{
    /**
     * @param array $data
     *
     * @throws OutOfOrderException
     */
    public function execute(array $data);
}