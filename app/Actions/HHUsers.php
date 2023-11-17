<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class HHUsers extends AbstractAction
{
    public function getTitle()
    {
        return 'download';
    }

    public function getIcon()
    {
        return 'voyager-download';
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
        return route('hhparseUser', ['user_id' => $this->data->id]);
    }

}
