<?php

namespace App\Bot\Conversations;

use App\Bot\Helper;
use App\Models\UserStorage;
use App\Services\ApiService;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;

class CouponConversation extends Conversation
{
    use Helper;

    public function __construct()
    {
        $this->userLogin();
    }

    public function get_coupon(){
        $this->ask(__('index.coupon.text'), function(Answer $answer) {
            $code = clear_encode($answer->getText());
            $validator = validate_field('code', $code, ['string', 'min:9']);

            if($validator->fails()) {
                $this->say(__('validation.custom.coupon.required'));
                return $this->repeat();
            }

            $coupon_store_response = ApiService::couponStore([
                'phone' => UserStorage::get('phone'),
                'code' => $code,
            ]);

            if($coupon_store_response->status) {
                $this->say(__('index.coupon.'.$coupon_store_response->success_text));
                return $this->bot->startConversation(new MenuConversation());
            } else {
                $this->say(__('validation.custom.coupon.required'));
                return $this->repeat();
            }
        }, $this->keyboard_back());
    }

    public function run() {
        $this->get_coupon();
    }
}
