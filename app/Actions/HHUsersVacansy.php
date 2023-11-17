<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class HHUsersVacansy extends AbstractAction
{
    public function getTitle()
    {
        return 'vacancy';
    }

    public function getIcon()
    {
        return 'voyager-refresh';
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

    public function shouldActionDisplayOnDataType()
    {
        return $this->dataType->slug == 'hh-users';
    }

    public function getDefaultRoute()
    {
        return route('get-vacancy', ['user_id' => $this->data->id]);
    }

}
