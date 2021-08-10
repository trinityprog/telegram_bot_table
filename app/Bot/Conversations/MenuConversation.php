<?php

namespace App\Bot\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\Drivers\Telegram\Extensions\Keyboard;
use BotMan\Drivers\Telegram\Extensions\KeyboardButton;

class MenuConversation extends Conversation
{
    public function keyboard()
    {
        $buttons = [
            KeyboardButton::create(icon_text('menu.profile')),
            KeyboardButton::create(icon_text('menu.start_action')), KeyboardButton::create(icon_text('menu.products')),
            KeyboardButton::create(icon_text('menu.send_question')), KeyboardButton::create(icon_text('menu.rules')),
            KeyboardButton::create(icon_text('menu.about_promo')), KeyboardButton::create(icon_text('menu.winners')),
            KeyboardButton::create(config('botman.icons.menu.language_'.app()->getLocale()).' '.__('index.menu.language')),
            KeyboardButton::create(icon_text('menu.site_link')),
        ];

        return Keyboard::create()
            ->type(Keyboard::TYPE_KEYBOARD)
            ->oneTimeKeyboard()
            ->resizeKeyboard()
            ->addRow($buttons[0])
            ->addRow($buttons[1], $buttons[2])
            ->addRow($buttons[3], $buttons[4])
            ->addRow($buttons[5], $buttons[6])
            ->addRow($buttons[7])
            ->addRow($buttons[8])
            ->toArray();
    }

    public function run()
    {
        return $this->say(__('index.menu_text'), $this->keyboard());
    }
}
