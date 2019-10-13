<?php

namespace App\Helpers;

class QueueProcess extends ProcessHelper
{
    protected static $opts = [
        "t:" => "tube:",
    ];

    public function getTube() : string
    {
        if (!empty($this->arguments['t'])) return $this->arguments['t'];

        if (!empty($this->arguments['tube'])) return $this->arguments['tube'];

        return $this->config->path("queue.tube");
    }
}