<?php

namespace Modules\Envato\Services\ReplacerBot;

use Modules\Envato\Services\ReplacerBot\ReplacerBot;
use Modules\Envato\Services\ReplacerBot\ScriptManagerResponseDTO as DTO;

final class ScriptManager
{
    protected static array $scripts = [
        'replace' => [
            'isActive' => false,
            'step' => 0,
            'data' => [],
            'messages' => [
                'channelId' ,
                'type'      ,
                'search'   ,
                'replace'   ,
                'start'   ,
                'end'     
            ]
        ]
    ];

    protected static bool $isStarted = false;
    protected static bool $isQuery = true;

    protected static string $currentScript = '';

    public static function startManage(string $scriptName) :void
    {
        self::$isStarted = true;
        if (self::$currentScript !== $scriptName) {
            if (array_key_exists(self::$currentScript, self::$scripts)) {
                if (self::$scripts[self::$currentScript]['step'] !== count(self::$scripts[self::$currentScript]['messages'])) {
                    self::clearData(self::$currentScript);
                }
            }
        }

        self::$currentScript = $scriptName;
        self::$scripts[self::$currentScript]['isActive'] = true;
    }

    public static function manage(array $request) :DTO
    {
        if (!self::$isStarted | !array_key_exists(self::$currentScript, self::$scripts)) {
            return new DTO(errorMessage: 'Something went wrong', isError: true);
        }

        $messages = [];

        $isResponse = false;
        $isEnd = false;

        $script = self::$scripts[self::$currentScript];
        $step = self::$scripts[self::$currentScript]['step'];


        if (!$script['isActive']) {
            return new DTO(errorMessage: 'Script is not active', isError: true);
        }

        if (self::$isQuery) {
            $messages[] = $script[$step];

            $isResponse = true;
        } else {
            if (!self::saveResponse($request)) {
                return new DTO(errorMessage: 'The given data was not saved', isError: true);
            }

            $messages[] = $script[$step];
        }

        if (self::nextStep()) {
            $isEnd = true;
        }

        self::$isQuery = !self::$isQuery;

        return new DTO($messages, $isResponse, $isEnd, $script['data']);
    }

    protected static function clearData(string $commandName) :void
    {
        foreach(ReplacerBot::$commands[$commandName] as $command)
        {
            ReplacerBot::$commands[$commandName][$command] = '';
        }
    }

    protected static function nextStep() :bool
    {
        $step = self::$scripts[self::$currentScript]['step'];
        $script = self::$scripts[self::$currentScript];

        if (self::$isQuery) {
            self::$scripts[self::$currentScript]['step'] = $step + 1;
        } else {
            self::$scripts[self::$currentScript]['step'] = ($step + 1) === count($script) ? $step : $step + 1;

            if ($step === self::$scripts[self::$currentScript]['step']) {
                return true;
            }
        }

        return false;
    }

    public static function saveResponse(array $request) :bool
    {
        if (!array_key_exists(self::$currentScript, ReplacerBot::$commands)) {
            return false;
        }

        $data = $request['message']['message'];

        self::$scripts[self::$currentScript]['data'][] = $data;

        if (array_search($data, self::$scripts[self::$currentScript]['data']) !== self::$scripts[self::$currentScript]['step']) {
            return false;
        }

        return true;
    }
}
