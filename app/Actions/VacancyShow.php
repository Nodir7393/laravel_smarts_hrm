<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class VacancyShow extends AbstractAction
{
    public function getTitle()
    {
        return 'open';
    }

    public function getIcon()
    {
        return 'voyager-documentation';
    }

    public function getPolicy()
    {
        return 'read';
    }

    public function getAttributes()
    {
        return [
            'class' => 'btn btn-sm btn-warning pull-right',
        ];
    }

    public function shouldActionDisplayOnDataType()
    {
        return $this->dataType->slug == 'hh-vacancies';
    }

    public function getDefaultRoute()
    {
        return route('hhparse', ['vacancy' => $this->data->id]);
    }

}
