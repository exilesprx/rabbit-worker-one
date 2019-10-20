<?php

namespace App\Repositories;

interface UserApplicationLayerContract
{
    public function findUserById(int $userId) : \App\AggregateRoots\UserApplicationLayerContract;
}