<?php

namespace Modules\Instagram\Service;


use Exception;
use Modules\ALL\Models\TgUser;
use Modules\Instagram\Entities\InstaPost;
use Modules\Instagram\Entities\InstaUser;

class InstaCommentService
{
    public $instagram;

    public function __construct() {
        $this->instagram = new InstaService();
    }

    public function get($post = null) {
        if(!is_null($post)){

        }
    }

    public function addComment($post, $comment){
        return $this->instagram->instagram->addComment($post, $comment);
    }

    public function deleteComment($mediaId, $commentId){
        $this->instagram->instagram->deleteComment($mediaId, $commentId);
    }

    public function check($array, $users, $id=null){
        $id = rand(0, count($users)-1);
        if(in_array($id, $array)){
            $id = rand(0, count($users)-1);
            $this->check($array, $users, $id);
        }else {
            return $id;
        }
    }

    public function tagUser() {
        $users = InstaUser::where('can_tag', 1)->get()->toArray();
        $count = rand(0, 5);
        $text = '';
        $tagged = [];
        for($i = 0; $i<=$count; $i++){
            $id = $this->check($tagged, $users);
            try{
                $text .= ' @' . $users[$id]['username'];
            }catch (Exception $e){
                print_r($e->getMessage());
            }
            $tagged[] = $id;
        }
        return $text;
    }

    public function getAvailablePosts(){
        return InstaPost::where('can_tag', 1)->get()->toArray();
    }
    
    public function tagUserToAllPosts(){
        $available_posts = $this->getAvailablePosts();
        foreach ($available_posts as $available_post) {

        }
    }
}
