<?php

namespace App\Bot;

use BotMan\Drivers\Telegram\Extensions\Keyboard;
use BotMan\Drivers\Telegram\Extensions\KeyboardButton;

class InlineKeyboardPaginator
{
    public $keyboard_array;

    public $first_page_label = '« {}';
    public $previous_page_label = '‹ {}';
    public $next_page_label = '{} ›';
    public $last_page_label = '{} »';
    public $current_page_label = '·{}·';

    public function build($paginator)
    {
        $keyboard_array = [];

        if ($paginator->lastPage() == 1) return $this->keyboard;

        else if ($paginator->lastPage() <= 5) {
            foreach (range(1, $paginator->lastPage() + 1) as $page)
                $keyboard_array[$page] = $page;
        }

        else
            $keyboard_array = $this->build_for_multi_pages($paginator);

        $keyboard_array[$paginator->currentPage()] = strtr($this->current_page_label, ['{}' => $paginator->currentPage()]);


        return $this->keyboard_to_array($keyboard_array);
    }

    public function build_for_multi_pages($paginator)
    {
        if($paginator->currentPage() <= 3)
            return $this->build_start_keyboard($paginator);

        else if($paginator->currentPage() > $paginator->lastPage() - 3)
            return $this->build_finish_keyboard($paginator);

        else
            return $this->build_middle_keyboard($paginator);
    }

    public function build_start_keyboard($paginator)
    {
        $keyboard_array = [];

        foreach (range(1, 4) as $page)
            $keyboard_array[$page] = $page;

        $keyboard_array[4] = strtr($this->next_page_label,["{}" => 4]);
        $keyboard_array[$paginator->lastPage()] = strtr($this->last_page_label, '{}', $paginator->lastPage());

        return $keyboard_array;
    }

    public function build_finish_keyboard($paginator)
    {
        $keyboard_array = [];

        $keyboard_array[1] = strtr($this->first_page_label, ['{}' => 1]);
        $keyboard_array[$paginator->lastPage() - 3] = strtr($this->previous_page_label, ['{}' => $paginator->lastPage() - 3]);

        foreach (range($paginator->lastPage() - 2, $paginator->lastPage() + 1) as $page)
            $keyboard_array[$page] = $page;

        return $keyboard_array;
    }

    public function build_middle_keyboard($paginator)
    {
        $keyboard_array = [];

        $keyboard_array[1] = strtr($this->first_page_label, ['{}' => 1]);
        $keyboard_array[$paginator->currentPage() - 1] = strtr($this->previous_page_label, ['{}' => $paginator->currentPage() - 1]);
        $keyboard_array[$paginator->currentPage()] = $paginator->currentPage();
        $keyboard_array[$paginator->currentPage() + 1] = strtr($this->next_page_label, ['{}' => $paginator->currentPage() + 1]);
        $keyboard_array[$paginator->lastPage()] = strtr($this->last_page_label, ['{}' => $paginator->lastPage()]);

        return $keyboard_array;
    }

//    public function keyboard_to_array($keyboard_array)
//    {
//        return Keyboard::create()->type(Keyboard::TYPE_INLINE)->addRow(
//            foreach ($keyboard_array as $key => $label) {
//                KeyboardButton::create($label)->callbackData($key)
//            }
//        )->toArray();
//    }
}
