<?php

namespace App\Bot\Middleware;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\Interfaces\Middleware\Sending;

class TypeWait implements Sending
{
    public $count = 0;

    public function sending($payload, $next, BotMan $bot)
    {
        if($this->count == 0) {
            $bot->typesAndWaits(1);
            $this->count++;
        }

        return $next($payload);
    }
}
