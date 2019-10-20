<?php

namespace App\AggregateRoots;

use App\Events\UserUpdatedEmail;
use App\Tasks\TaskConductor;

interface UserApplicationLayerContract
{
    public function save();

    public function updateUserEmail(UserUpdatedEmail $command);

    public function recordEvents(TaskConductor $conductor);
}