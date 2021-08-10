<?php

namespace App\Bot\Conversations;

use App\Bot\Helper;
use App\Models\City;
use App\Models\UserStorage;
use App\Services\ApiService;
use BotMan\BotMan\Messages\Attachments\File;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\Drivers\Telegram\Extensions\Keyboard;
use BotMan\Drivers\Telegram\Extensions\KeyboardButton;
use duke\helper\Rules\KZPhoneChecker;

class RegistrationConversation extends Conversation
{
    use Helper;

    public function keyboard_rules()
    {
        return Keyboard::create()->type(Keyboard::TYPE_INLINE)->addRow(
            KeyboardButton::create(__('index.keyboard.rules'))
                ->callbackData('/accept_rules')
        )->toArray();
    }

    public function keyboard_city_list()
    {

        $keyboard = Keyboard::create()->type(Keyboard::TYPE_INLINE);
        $cities = City::all();

        foreach ($cities as $city) {
            $keyboard->addRow(
                KeyboardButton::create(app()->getLocale() == 'ru' ? $city->name_ru : $city->name_kk)
                    ->callbackData($city->id)
            );
        }

        return $keyboard->toArray();
    }

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
        return Keyboard::create()->type(Keyboard::TYPE_INLINE)->addRow(
            KeyboardButton::create(__('index.keyboard.code'))->callbackData('/repeat_code')
        )->toArray();
    }

    public function send_rules(){
        $attachment = new File(rules_file());
        $message = OutgoingMessage::create()->withAttachment($attachment);

        $this->say($message);
        $this->ask(config('botman.icons.text.rules') .' '. __('index.ask.rules'), function (Answer $answer) {
            if ($answer->getValue() && $answer->getValue() == '/accept_rules') {
                $this->get_name();
            } else {
                return $this->repeat();
            }
        }, $this->keyboard_rules());
    }

    public function get_name(){
        $this->ask(config('botman.icons.text.name') .' '. __('index.ask.name'), function(Answer $answer) {
            $name = clear_encode($answer->getText());
            if($name == '/accept_rules') return $this->repeat();
            $validator = validate_field('name', $name, ['min:2']);

            if($validator->fails()) {
                $this->say($validator->messages()->first());
                return $this->repeat();
            }

            UserStorage::set(['name' => $name]);
            $this->get_phone();
        });
    }

    public function get_phone(){
        $this->ask(config('botman.icons.text.phone') .' '. __('index.ask.phone'), function(Answer $answer) {
            $phone = $answer->getText() == '%%%_CONTACT_%%%' ?
                    $answer->getMessage()->getContact()->getPhoneNumber() :
                    clear_encode($answer->getText());

            $validator = validate_field('phone', $phone, [new KZPhoneChecker]);

            if($validator->fails()) {
                $this->say($validator->messages()->first());
                return $this->repeat();
            }

            UserStorage::set(['phone' => kz_clear_phone($phone)]);
            $this->get_city();
        }, $this->keyboard_phone());
    }

    public function get_city(){
        $this->ask(__('index.ask.city'), function(Answer $answer) {
            if ($answer->getValue()) {
                UserStorage::set(['city_id' => $answer->getValue()]);
                if (ApiService::userRegister(UserStorage::get()->toArray())) {
                    return $this->get_code();
                }
                else {
                    $this->say(__('validation.custom.phone.unique'));
                    return $this->bot->startConversation(new ChooseAuthenticationConversation());
                }
            } else {
                return $this->repeat();
            }
        }, $this->keyboard_city_list());
    }

    public function get_code(){
        $this->say(__('index.authorization.code_sended', ['phone' => kz_format_phone(UserStorage::get('phone'))]), $this->keyboard_back());
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
                    $this->say(__('validation.exists', ['attribute' => 'code']));
                    return $this->repeat();
                }
            } else if ($answer->getValue() == '/repeat_code') {
                ApiService::userRestorePassword(['phone' => UserStorage::get('phone')]);
                return $this->repeat();
            }
        }, $this->keyboard_code());
    }

    public function send_text(){
        $this->say(config('botman.icons.text.success') .br(). __('index.authorization.success'));
        return $this->bot->startConversation(new MenuConversation());
    }

    public function run()
    {
        $this->send_rules();
    }
}
