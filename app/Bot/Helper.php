<?php

namespace App\Bot;

use App\Bot\Conversations\ChooseAuthenticationConversation;
use App\Models\UserStorage;
use App\Services\ApiService;
use BotMan\Drivers\Telegram\Extensions\Keyboard;
use BotMan\Drivers\Telegram\Extensions\KeyboardButton;
use BotMan\Drivers\Telegram\TelegramDriver;

trait Helper
{
    public function userLogin()
    {
        $user_storage = UserStorage::get();

        if (!ApiService::userLogin([
            'phone' => $user_storage->phone,
            'sms' => $user_storage->sms
        ])) {
            $bot = botman_create();
            $bot->startConversation(new ChooseAuthenticationConversation(), get_bot_chat()['id'], TelegramDriver::class);
            $bot->removeStoredConversation();
            return;
        }
    }

    public function keyboard_back()
    {
        return Keyboard::create()
            ->type(Keyboard::TYPE_KEYBOARD)
            ->resizeKeyboard()
            ->addRow(KeyboardButton::create(config('botman.icons.keyboard.back') .' '. __('index.keyboard.back')))
            ->toArray();
    }
}
