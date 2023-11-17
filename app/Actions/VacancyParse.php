<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class VacancyParse extends AbstractAction
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
        return $this->dataType->slug == 'hh-vacancies';
    }

    public function shouldActionDisplayOnRow($row)
    {
        return ($row->parsed !== 1);
    }

    public function getDefaultRoute()
    {
        return route('hhparse', ['vacancy' => $this->data->id]);
    }

}
