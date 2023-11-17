<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class VacancyParsed extends AbstractAction
{
    public function getTitle()
    {
        return ' reloading';
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
            'class' => 'btn btn-sm btn-warning pull-right',
        ];
    }

    public function shouldActionDisplayOnDataType()
    {
        return $this->dataType->slug == 'hh-vacancies';
    }

    public function shouldActionDisplayOnRow($row)
    {
        return ($row->parsed === 1);
    }

    public function getDefaultRoute()
    {
        return route('hhparse', ['vacancy' => $this->data->id]);
    }

}
