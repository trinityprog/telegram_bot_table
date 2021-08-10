<?php

namespace App\Bot\Conversations;

use App\Models\Winner;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\Drivers\Telegram\Extensions\Keyboard;
use BotMan\Drivers\Telegram\Extensions\KeyboardButton;
use Illuminate\Support\Str;

class StartConversation extends Conversation
{
    public function run()
    {
        $data = Winner::query()->paginate(30);

        $text = '';

        foreach ($data as $row) {
            $text .= $row->phone .' -- '. Str::upper($row->prize) . PHP_EOL;
        }

        return $this->say($text, $this->paginate($data));
    }

    public function paginate($pagination)
    {
        if($pagination->currentPage() != 1 || ($pagination->currentPage() < $pagination->lastPage())) {
            $prev_page = '';
            $next_page = '';

            if ($pagination->currentPage() <= 1) {
                $prev_page = KeyboardButton::create('<')->callbackData('/page='.($pagination->currentPage() > 1 ? $pagination->currentPage() - 1 : 1));
            }
            if ($pagination->currentPage() < $pagination->lastPage()) {
                $next_page = KeyboardButton::create('>')->callbackData('/page='.($pagination->currentPage() < $pagination->lastPage() ? $pagination->currentPage() + 1 : 1));
            }

            return Keyboard::create()->type(Keyboard::TYPE_INLINE)->addRow($prev_page, $next_page)->toArray();
        }
    }
}
