<?php

namespace App\Bot\Conversations;

use App\Models\UserStorage;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\Drivers\Telegram\Extensions\Keyboard;
use BotMan\Drivers\Telegram\Extensions\KeyboardButton;

class ChooseLanguageConversation extends Conversation
{
    public function keyboard() {
        return Keyboard::create()->type(Keyboard::TYPE_INLINE)->addRow(
            KeyboardButton::create('Русский')->callbackData('ru'),
            KeyboardButton::create('Казакша')->callbackData('kk')
        )->toArray();
    }

    public function run() {
        return $this->ask(__('index.choose_language.text'), function(Answer $answer) {
            if ($answer->getValue()) {
                app()->setLocale($answer->getValue());
                UserStorage::set(['lang' => $answer->getValue()]);

                return $this->bot->startConversation(new MenuConversation());
            }
            else $this->repeat();
        }, $this->keyboard());
    }
}
