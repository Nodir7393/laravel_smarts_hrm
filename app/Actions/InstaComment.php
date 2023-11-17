<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class InstaComment extends AbstractAction
{
    public function getTitle()
    {
        return 'Comment';
    }

    public function getIcon()
    {
        return 'voyager-bubble';
    }

    public function getPolicy()
    {
        return 'read';
    }

    public function getAttributes()
    {
        return [
            'class' => 'btn btn-sm btn-primary pull-right',
        ];
    }

    public function shouldActionDisplayOnDataType() {
        return $this->dataType->slug == 'insta-posts';
    }


    public function getDefaultRoute()
    {
        return route('comment-show', ['link' => $this->data->link]);
    }

}
