<?php

namespace Modules\ManagerBot\Services;


use danog\MadelineProto\bots;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\AddUser\Services\AddUserService;
use Modules\ALL\Models\TgChannel;
use Modules\ALL\Models\TgGroup;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\RunningMode\Webhook;
use SergiX44\Nutgram\Telegram\Attributes\MessageTypes;

/**
 * Class    ManagerBotService
 * @package Modules\ManagerBot\Services
 */
class ManagerBotService
{
    /**
     * @var int $step
     * orqaga knopkasini boskanda
     * qaysi joyga qaytishini aniqlash uchun
     * stepen
     */
    public int $step = 1;

    /**
     * @var Nutgram
     */
    public Nutgram $bot;

    /**
     * @var array|string[]
     * komandalar ro'yxati
     */
    public array $commands = [
        '/start',
        'O`zbekcha 🇺🇿',
        'Русский 🇷🇺',
        'English 🇺🇸',
    ];

    /**
     * @var array|string[]
     * botda ishlatilinadigan tillar
     * qisqartmasi kodimizda kere bo'ladi
     */
    public array $languages = [
        'uz',
        'ru',
        'en'
    ];

    /**
     * @var array|string[]
     * adminkani settings digi keyboard buttonlani
     * har xil tildigi zozuvlari
     */
    public array $setting_texts = [
        'add_to_channel_',
        'add_to_group_',
        'del_from_group_',
        'del_from_channel_',
        'lang_',
        'back_',
    ];

    /**
     * @var array $channels_title
     * kanallar nomi
     */
    public $channels_title;


    /**
     * @var $channels_id
     * kannalar idsi
     */
    public $channels_id;

    /**
     * @var array $channels_invite_link
     * kannalar linki
     */
    public $channels_invite_link;

    /**
     * @var array $groups_title
     * gruppalar nomi
     */
    public $groups_title;

    /**
     * @var array $groups_id
     * gruppalar idsi
     */
    public $groups_id;

    /**
     * @var array $groups_invite_link
     * gruppalar linki
     */
    public $groups_invite_link;

    /**
     * @var string|null $error
     * Qanday xatolik yuz bergani
     */
    public ?string $error;

    /**
     * @var string|null $error_descr
     * xatolikni izohi
     */
    public ?string $error_descr;

    public int $warning = 0;

    /**
     * @var string|null $lang
     * qaysi til tanlaganini bildiradi
     */
    public ?string $lang = null;

    /**
     *
     * Function  web
     * @void webhook ishlatilganda running mode ni
     * webhook ga o'zgartirip botni ishga tushiradi
     */
    public function web(): void
    {
        $this->bot->setRunningMode(Webhook::class);
        $this->handle($this->bot);

    }

    /**
     *
     * Function  console
     * @void artisan komanda bilan ishga
     * tushirilganda ishlatiladi
     */
    public function console(): void
    {
        $this->bot->deleteWebhook();
        $this->handle($this->bot);
    }

    /**
     *
     * Function  handle
     * @param Nutgram $bot
     * @void hamma botga keladigan requestlaga tipiga
     * qarap jovob qaytaradi
     */
    public function handle(Nutgram $bot): void
    {

        $bot->onCommand('start', function (Nutgram $bot) {
            $this->chooseLang($bot);
        });

        $bot->onText('{lang}', function (Nutgram $bot, $lang) {
            $this->onLang($bot, $lang);
        });

        $bot->onText('{user_id}', function (Nutgram $bot, $user_id) {
            $this->onUsereId($bot, $user_id);
        });

        $bot->onMessageType(MessageTypes::CONTACT, function (Nutgram $bot) {
            $updates = $bot->getUpdates();
            $user_id = $updates[0]->message->contact->user_id;
            $this->onUsereId($bot, $user_id);
        });

        foreach ($this->setting_texts as $setting_text) {
            foreach ($this->languages as $language) {
                $text = setting('managerbot.' . $setting_text . $language);

                switch ($setting_text) {
                    case ('add_to_channel_'):

                        $bot->onText($text, function (Nutgram $bot) {
                            $this->onAddToChannel($bot);
                        });
                        break;

                    case ('add_to_group_'):
                        $bot->onText($text, function (Nutgram $bot) {
                            $this->onAddToGroup($bot);
                        });
                        break;

                    case ('del_from_channel_'):
                        $bot->onText($text, function (Nutgram $bot) {
                            $this->onDelFromChannels($bot);
                        });
                        break;

                    case ('del_from_group_'):
                        $bot->onText($text, function (Nutgram $bot) {
                            $this->onDelFromGroups($bot);
                        });
                        break;


                    case ('lang_'):
                        $bot->onText($text, function (Nutgram $bot) use ($text) {
                            $this->chooseLang($bot);
                        });
                        break;

                    case ('back_'):
                        $bot->onText($text, function (Nutgram $bot) {
                            --$this->step;
                            $this->addButton($bot, $this->sendMessage());
                        });
                        break;
                }

                $this->commands[] = $text;
            }
        }


        $bot->onCallbackQueryData('add_channel{id}', function (Nutgram $bot, $id) {
            $add = new AddUserService();
            try {
                $add->addUser($id, Cache::get('user_id'));
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        });

        $bot->onCallbackQueryData('add_group{id}', function (Nutgram $bot, $id) {
            $add = new AddUserService();
            try {
                $add->addUser($id, Cache::get('user_id'));
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        });

        $bot->onCallbackQueryData('del_channel{id}', function (Nutgram $bot, $id) {
            try {
                $bot->banChatMember($id, Cache::get('user_id'));
            } catch (\Exception $e) {
                $bot->sendMessage($e->getMessage() . " " . $error = $bot->getChat($id)->invite_link ?? $id);
            }
        });

        $bot->onCallbackQueryData('del_group{id}', function (Nutgram $bot, $id) {
            try {
                $bot->banChatMember($id, Cache::get('user_id'));
            } catch (\Exception $e) {
                $bot->sendMessage($e->getMessage() . " " . $error = $bot->getChat($id)->invite_link ?? $id);
            }
        });

        /*$bot->onException(function (Nutgram $bot, \Throwable $exception) {
            Log::error($exception->getMessage());
            echo $exception->getMessage() . PHP_EOL;
            $bot->sendMessage($exception->getMessage());
        });*/

        $bot->run();

    }

    /**
     *
     * Function  checkLang
     * @param Nutgram $bot
     * @return  mixed|string|null
     */
    public function checkLang(Nutgram $bot): mixed
    {
        $get_lang = $this->lang ?? Cache::get('lang');

        if ($get_lang === null && $bot->message()->text !== '/start') {
            $this->chooseLang($bot);

            return null;
        }

        return $get_lang;
    }

    /**
     *
     * Function  chooseLang
     * @param Nutgram $bot
     */
    public function chooseLang(Nutgram $bot): void
    {
        $kb = ['reply_markup' =>
            ['keyboard' => [
                [
                    ['text' => 'O`zbekcha 🇺🇿'],
                    ['text' => 'Русский 🇷🇺'],
                    ['text' => 'English 🇺🇸'],
                ],
            ], 'resize_keyboard' => true]
        ];
        $bot->sendMessage("Здравствуйте! Добро пожаловать в наш бот!\nДавайте для начала выберем язык обслуживания!\n\nAssalomu aleykum! Botimizga xush kelibsiz!\nKeling, avvaliga xizmat ko’rsatish tilini tanlab olaylik.\n\nHello! Welcome to our Bot!\nLet's choose the language of service first", $kb);
    }

    /**
     *
     * Function  sendMessage
     */
    public function sendMessage(?string $condition = null): string
    {
        $step = $this->step;
        $lang = $this->lang ?? Cache::get('lang');

        switch (true) {
            case ($step === 2):
                $message = setting('managerbot.user_id_' . $lang);
                break;
            case ($step === 3):
                $message = setting('managerbot.choose_one_of_the_following_' . $lang);
                break;
            case ($step === 4 && $condition === 'delFromChannel'):
                $message = setting('managerbot.following_channels_' . $lang);
                break;
            case ($step === 4 && $condition === 'delFromGroup'):
                $message = setting('managerbot.following_groups_' . $lang);
                break;
            case ($step === 4 && $condition === 'addToChannel'):
                $message = setting('managerbot.add_following_channels_' . $lang);
                break;
            case ($step === 4 && $condition === 'addToGroup'):
                $message = setting('managerbot.add_following_groups_' . $lang);
                break;
            default:
                break;
        }

        return $message;
    }

    /**
     *
     * Function  onLang
     */
    public function onLang(Nutgram $bot, $lang): void
    {
        $languages = [
            'O`zbekcha 🇺🇿',
            'Русский 🇷🇺',
            'English 🇺🇸'
        ];

        if (in_array($lang, $languages, true)) {
            $this->step = 2;
        }

        switch ($lang) {
            case ($languages[0]):
                Cache::put('lang', 'uz');
                break;
            case ($languages[1]):
                Cache::put('lang', 'ru');
                break;
            case ($languages[2]):
                Cache::put('lang', 'en');
                break;
        }

        $this->lang = Cache::get('lang');

        $kb['reply_markup']['remove_keyboard'] = true;

        if (in_array($lang, $languages, true)) {
            $bot->sendMessage($this->sendMessage(), $kb);
        }
    }

    /**
     *
     * Function  onUsereId
     * @param Nutgram $bot
     * @param $user_id
     */
    public function onUsereId(Nutgram $bot, $user_id): void
    {
        $check = $this->checkLang($bot);

        if ($check) {
            if (is_numeric($user_id)) {
                $this->step = 3;
                Cache::put('user_id', $user_id);
                $this->addButton($bot, $this->sendMessage());
            } else if (!in_array($user_id, $this->commands, true)) {
                $bot->sendMessage(setting('managerbot.understand_you_' . $this->lang));
            }
        }
    }

    /**
     *
     * Function  onAddToChannel
     * @param Nutgram $bot
     */
    public function onAddToChannel(Nutgram $bot): void
    {
        if (Cache::get('user_id') !== null) {
            $user_id = Cache::get('user_id');
            $this->getUser($bot, $user_id, 0);
            $this->step = 4;
            $this->addButton($bot, $this->sendMessage('addToChannel'), 'addToChannel');
        } else {
            $this->step = 2;
            $bot->sendMessage($this->sendMessage());
        }
    }

    /**
     *
     * Function  onAddToGroup
     * @param Nutgram $bot
     */
    public function onAddToGroup(Nutgram $bot): void
    {
        if (Cache::get('user_id') !== null) {
            $user_id = Cache::get('user_id');
            $this->getUser($bot, $user_id, 0);
            $this->step = 4;
            $this->addButton($bot, $this->sendMessage('addToGroup'), 'addToGroup');
        } else {
            $this->step = 2;
            $bot->sendMessage($this->sendMessage());
        }
    }

    /**
     *
     * Function  onDelFromChannels
     * @param Nutgram $bot
     */
    public function onDelFromChannels(Nutgram $bot): void
    {
        if (Cache::get('user_id') !== null) {
            $user_id = Cache::get('user_id');
            $this->getUser($bot, $user_id, 1);
            $this->step = 4;
            $this->addButton($bot, $this->sendMessage('delFromChannel'), 'delFromChannel');
        } else {
            $this->step = 2;
            $bot->sendMessage($this->sendMessage());
        }
    }

    /**
     *
     * Function  onDelFromGroups
     * @param Nutgram $bot
     */
    public function onDelFromGroups(Nutgram $bot): void
    {
        if (Cache::get('user_id') !== null) {
            $user_id = Cache::get('user_id');
            $this->getUser($bot, $user_id, 1);
            $this->step = 4;
            $this->addButton($bot, $this->sendMessage('delFromGroup'), 'delFromGroup');
        } else {
            $this->step = 2;
            $bot->sendMessage($this->sendMessage());
        }
    }

    /**
     *
     * Function  onDelFromAll
     * @param Nutgram $bot
     */
    public function onDelFromAll(Nutgram $bot): void
    {
        if (Cache::get('user_id') !== null) {
            $user_id = Cache::get('user_id');
            $this->delFromChannel($bot, $user_id);
            $this->delFromGroup($bot, $user_id);
        } else {
            $bot->sendMessage('The first enter user id, please!');
        }
    }

    /**
     *
     * Function  getList
     * @return  array
     * basa danniydan dropper bot
     * bor kanal va gruppalani idlarini oberadi
     */
    public function getList(): array
    {
        $groups = TgGroup::where('bot_smarts_kick', 1)->pluck('tg_id')->toArray();
        $channels = TgChannel::where('bot_smarts_kick', 1)->pluck('tg_id')->toArray();

        return [
            "channels" => $channels,
            "groups" => $groups,
        ];
    }

    /**
     *
     * Function  getUser
     * @param Nutgram $bot
     * @param $user_id
     * @return  Nutgram
     * user bor kanal va gruppalani oberadi
     */
    public function getUser(Nutgram $bot, $user_id, $exist): Nutgram
    {
        $list = $this->getList();
        foreach ($list as $chats => $type) {
            foreach ($type as $chat => $key) {
                if ($key) {
                    $full = -100 . $key;
                    try {
                        $member = $bot->getChatMember((int)$full, $user_id);
                        if ($exist === 1) {
                            if ($member->status === 'member' || $member->status === 'administrator' || $member->status === 'restricted') {
                                $this->checkExist($bot, $key, $full, $chats);

                            }
                        } else if ($member->status !== 'member' && $member->status !== 'administrator' && $member->status !== 'restricted') {
                            $this->checkExist($bot, $key, $full, $chats);

                        }
                    } catch (\Exception $e) {
                        if ($this->warning !== 2) {

                            if ($chats === 'channels') {
                                $error = $e->getMessage() . ' ' . TgChannel::where('tg_id', $key)->value('invite_link');
                            } else {
                                $error = $e->getMessage() . ' ' . TgGroup::where('tg_id', $key)->value('invite_link');
                            }
                            $bot->sendMessage($error);
                            $this->warning = 1;
                        }
                    }
                }
            }

            if ($list["channels"][0] === "" && $list["groups"][0] === "") {
                $this->error_descr = setting('managerbot.channels_groups_ids_not_found_' . $this->lang);
                $this->error = "error";
            } else if ($type[0] === "") {
                $this->error_descr = "Not added $chats id from admin panel";
                $this->error = "not which one";
            }
        }
        if ($this->warning === 1) {
            $this->warning = 2;
        }
        return $bot;
    }


    /**
     *
     * Function  checkExist
     * @param Nutgram $bot
     * @param $key
     * @param $full
     * @param $chats
     */
    public function checkExist(Nutgram $bot, $key, $full, $chats): void
    {

        $title = $bot->getChat($full)->title ?? TgChannel::all()
            ->where('tg_id', $key)->get('title');
        $invite_link = $bot->getChat($full)->invite_link ?? TgGroup::all()
            ->where('tg_id', $key)->get('id');

        if ($chats === 'channels') {
            $this->channels_id[] = $full;
            $this->channels_title[] = $title;
            $this->channels_invite_link[] = $invite_link;
        } else {
            $this->groups_id[] = $full;
            $this->groups_title[] = $title;
            $this->groups_invite_link[] = $invite_link;
        }

    }

    /**
     *
     * Function  delFromChannel
     * @param Nutgram $bot
     * @param $user_id
     * kanaladan o'chirib beradi
     */
    public function delFromChannel(Nutgram $bot, $user_id): void
    {
        $this->getUser($bot, $user_id, 1);
        if ($this->channels_id !== null) {
            foreach ($this->channels_id as $item) {
                try {
                    $bot->banChatMember($item, $user_id);
                } catch (\Exception $e) {
                    $bot->sendMessage($e->getMessage() . " " . $error = $bot->getChat($item)->invite_link ?? $item);
                }
            }
            $bot->sendMessage('User deleted from channels');

            $this->channels_title = null;
            $this->groups_title = null;
        } else {
            $bot->sendMessage('The user does not exist on any channel');
        }

    }

    /**
     *
     * Function  delFromGroup
     * @param Nutgram $bot
     * @param $user_id
     * gruppaladan o'chirib beradi
     */
    public function delFromGroup(Nutgram $bot, $user_id): void
    {
        $this->getUser($bot, $user_id, 1);
        if ($this->groups_id !== null) {
            foreach ($this->groups_id as $item) {
                try {
                    $bot->banChatMember($item, $user_id);
                } catch (\Exception $e) {
                    $bot->sendMessage($e->getMessage() . " " . $error = $bot->getChat($item)->invite_link ?? $item);
                }

            }
            $bot->sendMessage('User deleted from groups');

            $this->channels_title = null;
            $this->groups_title = null;
        } else {
            $bot->sendMessage('The user does not exist on any group');
        }

    }

    /**
     *
     * Function  addButton
     * @param Nutgram $bot
     * buttonlani chiqarib beradi
     * vaziyatiga qarap
     */
    public function addButton(Nutgram $bot, $message, $action = null): void
    {
        $step = $this->step;
        $lang = $this->lang ?? Cache::get('lang');
        $kb = [];
        switch (true) {
            case ($step === 2):
                $kb['reply_markup']['remove_keyboard'] = true;
                break;
            case ($step === 3):
                $kb["reply_markup"]["keyboard"][] = [
                    [
                        "text" => setting('managerbot.add_to_channel_' . $lang)
                    ],
                    [
                        "text" => setting('managerbot.add_to_group_' . $lang),
                    ]
                ];
                $kb["reply_markup"]["keyboard"][] = [
                    [
                        "text" => setting('managerbot.del_from_channel_' . $lang)
                    ],
                    [
                        "text" => setting('managerbot.del_from_group_' . $lang),
                    ]
                ];
                $kb["reply_markup"]["keyboard"][] = [
                    [
                        "text" => setting('managerbot.lang_' . $lang)
                    ],
                    [
                        "text" => setting('managerbot.back_' . $lang),
                    ]
                ];
                $kb["reply_markup"]["resize_keyboard"] = true;
                break;
            case ($step === 4 && $action === 'addToChannel'):
                if ($this->channels_title !== null) {
                    foreach ($this->channels_title as $i => $item) {
                        $kb["reply_markup"]["inline_keyboard"][] = [
                            [
                                "text" => $item,
                                "url" => $this->channels_invite_link[$i] ?? 'https://google.com'
                            ],
                            [
                                "text" => '➕',
                                "callback_data" => 'add_channel' . $this->channels_id[$i]
                            ],
                        ];
                    }
                }
                break;
            case ($step === 4 && $action === 'addToGroup'):
                if ($this->groups_title !== null) {
                    foreach ($this->groups_title as $i => $item) {
                        $kb["reply_markup"]["inline_keyboard"][] = [
                            [
                                "text" => $item,
                                "url" => $this->groups_invite_link[$i] ?? 'https://google.com'
                            ],
                            [
                                "text" => '➕',
                                "callback_data" => 'add_channel' . $this->groups_id[$i]
                            ],
                        ];
                    }
                }
                break;
            case ($step === 4 && $action === 'delFromChannel'):
                if ($this->channels_title !== null) {
                    foreach ($this->channels_title as $i => $item) {
                        $kb["reply_markup"]["inline_keyboard"][] = [
                            [
                                "text" => $item,
                                "url" => $this->channels_invite_link[$i] ?? 'https://google.com'
                            ],
                            [
                                "text" => '❌',
                                "callback_data" => 'del_channel' . $this->channels_id[$i]
                            ],
                        ];

                    }
                }
                break;
            case ($step === 4 && $action === 'delFromGroup'):
                if ($this->groups_title !== null) {
                    foreach ($this->groups_title as $i => $item) {
                        $kb["reply_markup"]["inline_keyboard"][] = [
                            [
                                "text" => $item,
                                "url" => $this->groups_invite_link[$i] ?? 'https://google.com'
                            ],
                            [
                                "text" => '❌',
                                "callback_data" => 'del_channel' . $this->groups_id[$i]
                            ],
                        ];

                    }
                }
                break;
        }

        $bot->sendMessage($message, $kb);
        $kb = [];
        $this->channels_title = $this->channels_id = $this->channels_invite_link = null;
        $this->groups_title = $this->groups_id = $this->groups_invite_link = null;

    }

    /**
     * @void constructor
     */
    public function __construct()
    {
        $this->bot = new Nutgram(env('MANAGER_BOT_TOKEN'), ["timeout" => 60]);
    }
}
