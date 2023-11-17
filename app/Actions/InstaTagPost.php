<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class InstaTagPost extends AbstractAction
{
    public function getTitle()
    {
        return ' Tag users';
    }

    public function getIcon()
    {
        return 'voyager-window-list';
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
        return route('tag-users-show', ['link' => $this->data->link]);
    }

}
