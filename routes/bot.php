<?php

use App\Bot\Conversations\StartConversation;
use BotMan\BotMan\BotMan;

$bot = botman_create();

$bot->hears('/start', function (BotMan $bot) { return $bot->startConversation(new StartConversation()); });

$bot->fallback(function (BotMan $bot) { return $bot->reply('fallback'); });

$bot->listen();
