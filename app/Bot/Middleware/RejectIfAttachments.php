<?php

namespace App\Bot\Middleware;

use BotMan\BotMan\Interfaces\Middleware\Matching;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use BotMan\Drivers\Telegram\TelegramDriver;

class RejectIfAttachments implements Matching
{
    public function matching(IncomingMessage $message, $pattern, $regexMatched) // TODO добавить к $bot->hears где есть askImages
    {
        $reject_attachments = [
            '%%%_VIDEO_%%%',
            '%%%_AUDIO_%%%',
            '%%%_LOCATION_%%%',
            '%%%_CONTACT_%%%',
            '%%%_FILE_%%%'
        ];

        if (in_array($message->getText(), $reject_attachments)) {
            $bot = botman_create();
            $bot->say('Неверный формат сообщения', $message->getSender(), TelegramDriver::class);
            return false;
        }

        return $regexMatched;
    }
}
