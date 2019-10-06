<?php

namespace App\Helpers;

class QueueProcessHelper extends ProcessHelper
{
    protected static $opts = [
        "t:" => "tube:",
    ];

    public function getTube() : string
    {
        if (!empty($this->arguments['t'])) return $this->arguments['t'];

        if (!empty($this->arguments['tube'])) return $this->arguments['tube'];

        return "phalcon";
    }
}