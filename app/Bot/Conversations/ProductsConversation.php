<?php

namespace App\Bot\Conversations;

use BotMan\BotMan\Messages\Attachments\File;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;

class ProductsConversation extends Conversation
{
    public function run() {
        $attachment = new File(env('APP_URL').'docs/products.pdf?t='.time());
        $message = OutgoingMessage::create()->withAttachment($attachment);

        $this->say($message);
    }
}
