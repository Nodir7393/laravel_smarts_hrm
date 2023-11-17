<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class InstaLogin extends AbstractAction
{
    public function getTitle()
    {
        return 'login';
    }

    public function getIcon()
    {
        return 'voyager-lock';
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
        return $this->dataType->slug == 'insta-bots';
    }

    public function shouldActionDisplayOnRow($row)
    {
        return ($row->logined !== 1);
    }

    public function getDefaultRoute()
    {
        return route('bot-login', ['id' => $this->data->id]);
    }

}
