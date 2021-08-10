<?php

namespace App\Bot\Conversations;

use App\Bot\InlineKeyboardPaginator;
use App\Models\Winner;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use Illuminate\Support\Collection;

class StartConversation extends Conversation
{
    public function data($page = 1)
    {
        $data = Winner::query()->paginate(30, ['*'], '', $page);
        $text = '';

        foreach ($data as $row) {
            $text .= $row->created_at->format('d.m.Y') .' '. $row->phone .' '. $row->city . PHP_EOL;
        }

        return [
            'text' => $text,
            'keyboard' => (new InlineKeyboardPaginator)->build($data)
        ];
    }
    public function run()
    {
        $data = $this->data();

        return $this->ask($data['text'], function(Answer $answer){
            $data = $this->data($answer->getValue());

            $this->bot->sendRequest('editMessageText', [
                'message_id' => $this->bot->getMessage()->getPayload()['message_id'],
                'text' => $data['text']
            ] + $data['keyboard']);
        }, $data['keyboard']);
    }
}
