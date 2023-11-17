<?php

namespace App\Services\MadelineProto;

use Amp\Loop;
use App\Services\ALL\MTProtoService;
use App\Services\SearchService;
use Modules\EnvatoAddBot\Services\TaskStatusService;
use danog\MadelineProto\API;

class ActiveTaskFinder
{
    public $madelineProto;
    public $searchService;
    public $last;

    public function __construct()
    {
        $this->madelineProto = new MTProtoService();
        $this->searchService = new SearchService();
    }

    public function taskFind(/*$channel, $task, $inputChannel*/){       //NOT COMPLETED DON'T TOUCH IT!!!

        $channel = -1001736380151;                                      //Channel or group ID to find task
        $task = ['ActiveTask', 'WebApp', 'BookForMe', 'Modules'];     //TaskName to search
        $inputChannel = -1001866423428;                                 //Input message which we want to create task links

        $last = $this->madelineProto->madelineproto->messages->getHistory(['peer' => $channel, 'limit' => 1])['messages'][0]['id'];
        $last1 = $this->madelineProto->madelineproto->messages->getHistory(['peer' => $inputChannel, 'limit' => 1])['messages'][0]['id'];
        $loopix = true;
        $result = [];
        while ($loopix) {
            $tasks = $this->madelineProto->madelineproto->messages->getHistory(['peer' => $channel, 'limit' => 100, 'offset_id' => $last+1]);

            $tsks = $tasks['messages'];     //messages received
            $count = count($tsks);          //amount of received messages

            foreach ($tsks as $message) {

                if ($message['_'] === "message") {
                    $msgword = explode(' ', $message['message']);
                    $id = $message['id'];
                    foreach ($task as $hashtag) {
                        if(!array_key_exists($hashtag, $result)){
                            $result[$hashtag] = "";
                        }

                        if (str_contains($message['message'], "#".$hashtag)){
                            $tag = '';
                            foreach ($msgword as $word){
                                if (str_starts_with($word, '@')){
                                    $tag .= " ".$word;
                                }
                            }

                            $chan = substr($channel,4);        //short channel or group id
                            $link= "https://t.me/c/$chan/$id";       //link of found post
                            $result[$hashtag] .= $link.$tag . "\n";
                        }
                    }
                }
                $last = $message['id'];
            }
            if ($count === 100){
                echo "\n\n    Synced messages:$count\n    Searching...\n\n";    //will be deleted after completed
                sleep(2);
            } else{
                echo "\n\nFinished\n    Checked:$count\n\n";                    //will be deleted after completed
                $loop = true;
                while ($loop){

                    $posts = $this->madelineProto->madelineproto->messages->getHistory(['peer' => $inputChannel, 'limit' => 100,
                        'offset_id' => $last1+1]);
                    foreach ($posts['messages'] as $post){
                        if ($post['_'] === "message") {
                            $check = "";
                            foreach ($task as $hashtag) {
//                                echo "\n\n\n\n_________________" . $post['message'] . "\n_________________" . $hashtag;
                                if (str_contains($post['message'], $hashtag)){
                                    $edition = $hashtag."\n\n".$result[$hashtag];
                                    echo "\n_________________\n  Hashtag:$hashtag\n  NewMsg:$edition\n\n OldMsg:".$post['message'];
                                    if (!str_contains($post['message'], $edition)){
                                        echo " not equal";
//                                        $this->madelineProto->MadelineProto->messages->editMessage(['peer' => $inputChannel, 'id'=>$post['id'],
//                                            'message'=> $edition]);
                                    } else{
                                        echo "else";
                                    }
                                    $check .= $hashtag;
                                }
                            }
                            foreach ($task as $hashtag){
                                if (!str_contains($check, $hashtag)){

                                    $edition = $hashtag."\n\n".$result[$hashtag];
                                    //$this->madelineProto->MadelineProto->messages->sendMessage(['peer' =>$inputChannel, 'message' => $edition]);
                                }
                            }
                        }
                        $last1 = $post['id'];
                    }
                    $msgnm = $posts['messages'];     //messages received
                    $countt = count($msgnm);          //amount of received messages
                    echo "\n\n\namount of received messages:".$countt."\n\n\n";
                    if ($countt != 100){
                        $loop = false;
                        echo "loop finished";
                    }
                }
//                print_r($result);
                echo "loopix finished";
                break;
            }
        }
    }
}
