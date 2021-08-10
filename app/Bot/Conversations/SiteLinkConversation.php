<?php

namespace App\Bot\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\Drivers\Telegram\Extensions\Keyboard;
use BotMan\Drivers\Telegram\Extensions\KeyboardButton;

class SiteLinkConversation extends Conversation
{
    public function keyboard() {
        return Keyboard::create()->type(Keyboard::TYPE_INLINE)->addRow(
            KeyboardButton::create(config('app.url'))->url(config('app.url'))
        )->toArray();
    }

    public function run() {
        return $this->say(__('index.site_link_text'), $this->keyboard());
    }
}
