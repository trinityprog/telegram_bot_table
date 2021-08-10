<?php

namespace App\Bot\Conversations;

use App\Bot\Helper;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\Drivers\Telegram\Extensions\Keyboard;
use BotMan\Drivers\Telegram\Extensions\KeyboardButton;

class ChooseAuthenticationConversation extends Conversation
{
    use Helper;

    public function keyboard() {
        return Keyboard::create()->type(Keyboard::TYPE_INLINE)->addRow(
            KeyboardButton::create(__('index.keyboard.authorization'))->callbackData('/authorization'),
            KeyboardButton::create(__('index.keyboard.registration'))->callbackData('/registration')
        )->toArray();
    }

    public function run() {
        $this->say(__('index.choose_auth.alert'), $this->keyboard_back());
        $this->say(__('index.choose_auth.text'), $this->keyboard());
    }
}
