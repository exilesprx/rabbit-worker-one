<?php

namespace App\Helpers;

class AmqpProcessHelper extends ProcessHelper
{
    protected static $opts = [
        "t:" => "tube:",
        "q:" => "queue:",
        "e:" => "exchange:"
    ];

    public function getTube() : string
    {
        if (!empty($this->arguments['t'])) return $this->arguments['t'];

        if (!empty($this->arguments['tube'])) return $this->arguments['tube'];

        return "phalcon";
    }

    public function getQueue() : string
    {
        if (!empty($this->arguments['q'])) return $this->arguments['q'];

        if (!empty($this->arguments['queue'])) return $this->arguments['queue'];

        return "task_queue.one";
    }

    public function getExchange() : string
    {
        if (!empty($this->arguments['e'])) return $this->arguments['e'];

        if (!empty($this->arguments['exchange'])) return $this->arguments['exchange'];

        return "worker_one";
    }
}