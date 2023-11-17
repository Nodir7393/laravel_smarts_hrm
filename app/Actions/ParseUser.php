<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class ParseUser extends AbstractAction
{
    public function getTitle()
    {
        return 'parse';
    }

    public function getIcon()
    {
        return 'voyager-cloud-download';
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
        return $this->dataType->slug == 'insta-users';
    }

    public function shouldActionDisplayOnRow($row)
    {
        return (false);
    }

    public function getDefaultRoute()
    {
        return route('show-parse-users', ['username' => $this->data->username, 'id' => $this->data->id]);
    }

}
