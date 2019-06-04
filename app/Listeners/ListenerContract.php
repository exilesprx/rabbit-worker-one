<?php


namespace App\Listeners;


use Phalcon\Events\Event;

interface ListenerContract
{
    public function handle(Event $event, $task);
}