<?php

namespace App\Bot\Conversations;

use App\Services\ApiService;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\Drivers\Telegram\Extensions\Keyboard;
use BotMan\Drivers\Telegram\Extensions\KeyboardButton;
use Carbon\Carbon;

class WinnersWeeksConversation extends Conversation
{
    public function keyboard() {
        $weeks = [
            ['15.04.21 – 22.04.21', '2021-04-23'],
            ['23.04.21 – 30.04.21', '2021-05-04'],
            ['01.05.21 – 07.05.21', '2021-05-11'],
            ['08.05.21 – 15.05.21', '2021-05-18'],
            ['16.05.21 – 24.05.21', '2021-05-25'],
            ['25.05.21 – 01.06.21', '2021-06-02'],
        ];


        $keyboard =  Keyboard::create()
            ->type(Keyboard::TYPE_INLINE);

        foreach ($weeks as $week) {

            if(Carbon::parse($week[1])->endOfDay()->isPast()) {
                $keyboard->addRow(
                    KeyboardButton::create($week[0])->callbackData('/winners_list/' . $week[1])
                );
            }
        }

        dump($keyboard->toArray());
        return $keyboard->toArray();
    }

    public function run() {
        $this->say(__('index.winners_text'), $this->keyboard());
    }
}
