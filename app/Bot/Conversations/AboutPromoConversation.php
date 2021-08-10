<?php

namespace App\Bot\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;

class AboutPromoConversation extends Conversation
{
    public function run() {
        return $this->say(__('index.about_promo_text'));
    }
}
