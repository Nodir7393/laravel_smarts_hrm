<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class InstaLike extends AbstractAction
{
    public function getTitle()
    {
        return 'Like';
    }

    public function getIcon()
    {
        return 'voyager-heart';
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

    public function shouldActionDisplayOnRow($row)
    {
        return ($row->hasLiked == 0);
    }

    public function getDefaultRoute()
    {
        return route('show-one-like', ['link' => $this->data->link]);
    }

}
