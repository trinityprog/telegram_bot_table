<?php

namespace App\Bot\Conversations;

use App\Bot\Helper;
use App\Models\UserStorage;
use App\Services\ApiService;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\Drivers\Telegram\Extensions\Keyboard;
use BotMan\Drivers\Telegram\Extensions\KeyboardButton;

class SendQuestionConversation extends Conversation
{
    use Helper;

    public function __construct()
    {
        $this->userLogin();
    }

    public function keyboard_confirm()
    {
        return Keyboard::create()
            ->type(Keyboard::TYPE_INLINE)
            ->addRow(
                KeyboardButton::create(__('index.keyboard.send'))->callbackData('/send'),
                KeyboardButton::create(__('index.keyboard.edit'))->callbackData('/edit')
            )
            ->toArray();
    }

    public function get_question()
    {
        return $this->ask(__('index.send_question.text'), function(Answer $answer) {
            $question = clear_encode($answer->getText());
            $validator = validate_field('question', $question, ['min:8', 'max:190']);

            if($validator->fails()) {
                $this->say($validator->messages()->first());
                return $this->repeat();
            }

            return $this->confirm_question($question);
        }, $this->keyboard_back());
    }

    public function confirm_question($question = '-')
    {
        return $this->ask(__('index.send_question.confirm', ['question' => $question]), function(Answer $answer) use ($question) {
            if ($answer->getValue() == '/send') {
                ApiService::questionSend([
                    'phone' => UserStorage::get('phone'),
                    'question' => $question
                ]);
                $this->say(__('index.send_question.success'));
                return $this->bot->startConversation(new MenuConversation());
            }

            if ($answer->getValue() == '/edit')
                return $this->get_question();
        }, $this->keyboard_confirm());
    }

    public function run()
    {
        $this->get_question();
    }

}
