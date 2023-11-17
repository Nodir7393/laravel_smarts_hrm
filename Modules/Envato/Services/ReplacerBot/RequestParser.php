<?php

namespace Modules\Envato\Services\ReplacerBot;

 final class RequestParser
{

    use CommandParser;

    /**
     *
     * MadeLineProto $update
     *
     * @var array
     */
    protected static array $request;

     /**
      * @param array $request
      * @return void
      */
     public static function setRequest(array $request) :void
    {
        self::$request = $request;
    }

    public static function checkIsEmpty() :bool
    {
        if (self::$request['message']['_'] === 'messageEmpty' || self::$request['message']['out'] ?? false) {
            return false;
        }

        return true;
    }

    public function getPeerIdIfExists() :int | null
    {
        return  (int)array_key_exists('user_id', self::$request['message']['peer_id']) ? self::$request['message']['peer_id']['user_id'] : NULL;
    }
}
