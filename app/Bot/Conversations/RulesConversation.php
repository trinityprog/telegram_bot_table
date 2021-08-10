<?php

namespace App\Bot\Conversations;

use BotMan\BotMan\Messages\Attachments\File;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;

class RulesConversation extends Conversation
{
    public function run() {
        $attachment = new File(rules_file());
        $message = OutgoingMessage::create(config('botman.icons.text.rules') .' '. __('index.ask.rules'))->withAttachment($attachment);

        $this->say($message);
    }
}
