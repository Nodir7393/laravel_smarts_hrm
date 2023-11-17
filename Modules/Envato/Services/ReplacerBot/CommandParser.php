<?php

namespace Modules\Envato\Services\ReplacerBot;

trait CommandParser
{
    public static function getCommand() :string
    {
        $command = '';
        $messageArray = explode(' ', RequestParser::$request['message']['message']);

        foreach ($messageArray as $message)
        {
            if (str_contains($message, '/')) {
                $command = $message;
            }
        }

        return $command;
    }

    public static function getParsedCommand() :string
    {
        $command = self::getCommand() . ' ';

        foreach(ReplacerBot::$commands[$command] as $command => $option) 
        {
            $command .= "--$command=$option ";
        }

        return $command;
    } 

    public static function checkIsCommand() :bool
    {
        if (!str_contains(RequestParser::$request['message']['message'], '/')) {
            return false;
        }

        return true;
    }

    public static function checkIsMatchedCommand() :bool
    {
        if (array_key_exists(self::getCommand(), ReplacerBot::$commands)) {
            return true;
        }

        return false;
    }
}
