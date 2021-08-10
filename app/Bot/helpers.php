<?php

use App\Rules\TelegramTextChecker;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Cache\LaravelCache;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\Drivers\Telegram\TelegramDriver;

if (! function_exists('botman_create')) {
    function botman_create() {
        $config = [
            'telegram' => config('botman')
        ];
        DriverManager::loadDriver(TelegramDriver::class);
        return BotManFactory::create($config, new LaravelCache());
    }
}

if (! function_exists('get_bot_chat')) {
    function get_bot_chat() {
        return request()->input('message.chat') ?? request()->input('callback_query.message.chat');
    }
}

if (! function_exists('rules_file')) {
    function rules_file() {
        return env('APP_URL').'docs/rules_'.app()->getLocale().'.pdf?t='.time();
    }
}

if (! function_exists('icon_text')) {
    function icon_text($key) {
        return config('botman.icons.'.$key) .' '. __('index.'.$key);
    }
}

if (! function_exists('clear_encode')) {
    function clear_encode($string) {
        return preg_replace('%(?:
              \xF0[\x90-\xBF][\x80-\xBF]{2}      # planes 1-3
            | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
            | \xF4[\x80-\x8F][\x80-\xBF]{2}      # plane 16
        )%xs', '', iconv("UTF-8", "UTF-8//IGNORE", $string));
    }
}

if (! function_exists('validate_field')) {
    function validate_field($field, $value, $rules = []) {
        $data = [$field => $value];

        return Validator::make($data, [
            $field => array_merge($rules, ['required', new TelegramTextChecker()])
        ]);
    }
}

if (! function_exists('br')) {
    function br() {
        return PHP_EOL;
    }
}
