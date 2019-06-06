<?php

namespace App\Tasks;

interface TaskContract
{
    public function execute(array $data);
}