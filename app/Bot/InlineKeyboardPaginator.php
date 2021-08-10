<?php

namespace App\Bot;

use BotMan\Drivers\Telegram\Extensions\Keyboard;
use BotMan\Drivers\Telegram\Extensions\KeyboardButton;

class InlineKeyboardPaginator
{
    public $keyboard_array = [];

    public $first_page_label = '« {}';
    public $previous_page_label = '‹ {}';
    public $next_page_label = '{} ›';
    public $last_page_label = '{} »';
    public $current_page_label = '-{}-';

    public function build($paginator, $pageName = '{}')
    {
        if ($paginator->lastPage() == 1) return $this->keyboard_array;

        else if ($paginator->lastPage() <= 5) {
            foreach (range(1, $paginator->lastPage() + 1) as $page)
                $this->keyboard_array[$page] = $page;
        }

        else $this->keyboard_array = $this->build_for_multi_pages($paginator);

        $this->keyboard_array[$paginator->currentPage()] = strtr($this->current_page_label, ['{}' => $paginator->currentPage()]);


        return $this->keyboard_to_array($this->keyboard_array, $pageName);
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
        foreach (range(1, 4) as $page)
            $this->keyboard_array[$page] = $page;

        $this->keyboard_array[4] = strtr($this->next_page_label,["{}" => 4]);
        $this->keyboard_array[$paginator->lastPage()] = strtr($this->last_page_label, '{}', $paginator->lastPage());

        return $this->keyboard_array;
    }

    public function build_finish_keyboard($paginator)
    {
        $this->keyboard_array[1] = strtr($this->first_page_label, ['{}' => 1]);
        $this->keyboard_array[$paginator->lastPage() - 3] = strtr($this->previous_page_label, ['{}' => $paginator->lastPage() - 3]);

        foreach (range($paginator->lastPage() - 2, $paginator->lastPage()) as $page)
            $this->keyboard_array[$page] = $page;

        return $this->keyboard_array;
    }

    public function build_middle_keyboard($paginator)
    {
        $this->keyboard_array[1] = strtr($this->first_page_label, ['{}' => 1]);
        $this->keyboard_array[$paginator->currentPage() - 1] = strtr($this->previous_page_label, ['{}' => $paginator->currentPage() - 1]);
        $this->keyboard_array[$paginator->currentPage()] = $paginator->currentPage();
        $this->keyboard_array[$paginator->currentPage() + 1] = strtr($this->next_page_label, ['{}' => $paginator->currentPage() + 1]);
        $this->keyboard_array[$paginator->lastPage()] = strtr($this->last_page_label, ['{}' => $paginator->lastPage()]);

        return $this->keyboard_array;
    }

    public function keyboard_to_array($keyboard_array, $pageName)
    {
        foreach ($keyboard_array as $key => $label) $buttons[] = KeyboardButton::create($label)->callbackData(strtr($pageName, ['{}' => $key]));
        return Keyboard::create()->type(Keyboard::TYPE_INLINE)->addRow(...$buttons)->toArray();
    }
}
