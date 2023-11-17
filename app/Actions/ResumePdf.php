<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class ResumePdf extends AbstractAction
{
    public function getTitle()
    {
        return 'pdf';
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

    public function shouldActionDisplayOnRow($row)
    {
        return ($row->is_pdf === 1);
    }

    public function getDefaultRoute()
    {
        return route('hhpdf', ['resume_id' => $this->data->resume_id]);
    }

}
