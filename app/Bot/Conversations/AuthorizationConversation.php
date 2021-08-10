<?php

namespace App\Bot\Conversations;

use App\Bot\Helper;
use App\Models\UserStorage;
use App\Services\ApiService;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\Drivers\Telegram\Extensions\Keyboard;
use BotMan\Drivers\Telegram\Extensions\KeyboardButton;
use duke\helper\Rules\KZPhoneChecker;

class AuthorizationConversation extends Conversation
{
    use Helper;

    public function keyboard_phone()
    {
        return Keyboard::create()
            ->type(Keyboard::TYPE_KEYBOARD)
            ->oneTimeKeyboard(true)
            ->resizeKeyboard()
            ->addRow(KeyboardButton::create(icon_text('keyboard.phone'))->requestContact(true))
            ->addRow(KeyboardButton::create(config('botman.icons.keyboard.back') .' '. __('index.keyboard.back')))
            ->toArray();
    }

    public function keyboard_code()
    {
        return Keyboard::create()
            ->type(Keyboard::TYPE_INLINE)
            ->addRow(KeyboardButton::create(__('index.keyboard.code_new'))->callbackData('/repeat_code'))
            ->toArray();
    }

    public function get_phone(){
        $this->ask(config('botman.icons.text.phone') .' '. __('index.ask.phone'), function(Answer $answer) {
            $phone = $answer->getText() == '%%%_CONTACT_%%%' ?
                     $answer->getMessage()->getContact()->getPhoneNumber() :
                     clear_encode($answer->getText());

            $validator = validate_field('phone', $phone, [new KZPhoneChecker()]);

            if($validator->fails()) {
                $this->say($validator->messages()->first());
                return $this->repeat();
            }

            $phone = kz_clear_phone($phone);
            UserStorage::set(['phone' => $phone]);
            return $this->get_code();
        }, $this->keyboard_phone());
    }

    public function get_code(){
        $this->say(__('index.authorization.code_sended', ['phone' => UserStorage::get('phone')]), $this->keyboard_back());
        $this->ask(config('botman.icons.text.code') .' '. __('index.ask.code'), function(Answer $answer) {
            if (empty($answer->getValue())) {
                $code = clear_encode($answer->getText());
                $validator = validate_field('code', $code, ['min:4']);

                if($validator->fails()) {
                    $this->say($validator->messages()->first());
                    return $this->repeat();
                }

                $user_storage = UserStorage::set(['sms' => $code]);
                if(ApiService::userLogin([
                    'phone' => $user_storage->phone,
                    'sms' => $user_storage->sms,
                ])) {
                    return $this->send_text();
                } else {
                    $this->say(__('validation.custom.code.exists'));
                    return $this->repeat();
                }
            } else if ($answer->getValue() == '/repeat_code') {
                ApiService::userRestorePassword(['phone' => UserStorage::get('phone')]);
                return $this->repeat();
            }
        }, $this->keyboard_code());
    }

    public function send_text(){
        return $this->bot->startConversation(new MenuConversation());
    }

    public function run()
    {
        $this->get_phone();
    }
}
