<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class Resume extends AbstractAction
{
    public function getTitle()
    {
        return 'view';
    }

    public function getIcon()
    {
        return 'voyager-eye';
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
        return $this->dataType->slug == 'hh-resumes';
    }

    public function getDefaultRoute()
    {
        return route('hhparse', ['resume' => $this->data->id]);
    }

}
