<?php

namespace Modules\TaskStatus\Service;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Modules\ALL\Services\MTProtoService;

class DuplicateService
{
    public MTProtoService $MTProto;

    public function __construct()
    {
        $this->MTProto = new MTProtoService();
    }

    public function Duplicate($output, $channel_id, $start): void
    {
        $duplicates = [];
        $offset = $this->MTProto->madelineproto->messages->getHistory(['peer' => $channel_id, 'limit' => 1])['messages'];
        $end = Arr::get($offset, 0)['id'];

        for ($i = $start; $i < $end; $i = $i + 100) {
            $messages = $this->MTProto->madelineproto->messages->getHistory(['peer' => $channel_id, 'limit' => 100]);
            foreach ($messages['messages'] as $message) {
                if (Arr::exists($message, 'media')) {
                    if (Arr::exists($message['media'], 'document')) {
                        $duplicates[json_encode($message['media']['document']['size'], true)][] = $message['id'];
                    }
                    if (Arr::exists($message['media'], 'photo')) {
                        $duplicates[json_encode($message['media']['photo']['sizes'], true)][] = $message['id'];
                    }
                }
            }
            foreach ($duplicates as $duplicate) {
                if (count($duplicate) > 1) {
                    foreach ($duplicate as $key => $msg_id) {
                        $duplicate[$key] = $this->MTProto->madelineproto->channels->exportMessageLink(['channel' => $channel_id, 'id' => $msg_id])['link'];
                    }
                    $output->info($duplicate);
                }
            }
        }
    }
}
