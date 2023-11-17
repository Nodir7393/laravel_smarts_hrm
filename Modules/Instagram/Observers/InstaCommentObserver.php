<?php

namespace Modules\Instagram\Observers;

use Modules\ALL\Services\LoginService;
use Modules\Instagram\Entities\InstaComment;
use Modules\Instagram\Service\InstaCommentService;

class InstaCommentObserver
{
    public $commentService;

    public function __construct() {
        $this->commentService = new InstaCommentService();
    }

    public function created(InstaComment $comment){

        $response = $this->commentService->addComment($comment->insta_post_id, $comment->text);
        $validated = $this->commentService->instagram->validate($response);
        $db = InstaComment::find($comment->id);
        $db->update($validated);
    }
    public function deleted(InstaComment $comment){
        $this->commentService->deleteComment($comment->insta_post_id, $comment->insta_id);
    }
}
