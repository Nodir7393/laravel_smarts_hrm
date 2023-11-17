<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace Modules\ManagerBot\Services;


use SergiX44\Nutgram\Conversations\InlineMenu;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;

class InlineButtonService extends InlineMenu
{
//    public function deleteFrom(Nutgram $bot, $message, $button_text, $callback_data)
//    {
//        $menu_text = $this->menuText($message);
//
//        foreach ($button_text as $i => $item) {
//            $this->addButtonRow(InlineKeyboardButton::make($item, callback_data:$callback_data.'@handleChat'));
//        }
//
//        $this->orNext('none')->showMenu();
//    }

    public function start(Nutgram $bot)
    {
        $this->menuText('Choose a color:')
            ->addButtonRow(InlineKeyboardButton::make('Red', callback_data: 'red@handleColor'))
            ->addButtonRow(InlineKeyboardButton::make('Green', callback_data: 'green@handleColor'))
            ->addButtonRow(InlineKeyboardButton::make('Yellow', callback_data: 'yellow@handleColor'))
            ->orNext('none')
            ->showMenu();
    }

    public function handleColor(Nutgram $bot)
    {
        $color = $bot->callbackQuery()->data;
        $this->menuText("Choosen: $color")
            ->showMenu();
    }
}
