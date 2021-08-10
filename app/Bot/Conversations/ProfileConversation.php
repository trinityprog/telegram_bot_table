<?php

namespace App\Bot\Conversations;

use App\Bot\Helper;
use App\Models\UserStorage;
use App\Services\ApiService;
use BotMan\BotMan\Messages\Conversations\Conversation;

class ProfileConversation extends Conversation
{
    use Helper;

    public function __construct()
    {
        $this->userLogin();
    }

    public function run()
    {
        $userProfile = ApiService::userProfile([
            'phone' => UserStorage::get('phone'),
            'lang' => UserStorage::get('lang'),
        ]);

        if($userProfile->status) {
            $userProfile = $userProfile->data;

            $info = __('index.profile.user_data', [
                'name' => $userProfile->name,
                'phone' => $userProfile->phone,
                'city' => $userProfile->city,
            ]);
            $this->bot->reply($info, $this->keyboard_back());

            $coupons = $userProfile->coupons;
            if(count($coupons)) {
                $coupons_info = __('index.profile.my_coupons');

                foreach ($coupons as $code) {
                    $coupons_info .= br() . br() . $code->registered_at . br() . $code->code;
                }
                $this->bot->reply($coupons_info);
            }
        }
    }
}
