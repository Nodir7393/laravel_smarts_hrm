<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class ResumeDoc extends AbstractAction
{
    public function getTitle()
    {
        return 'doc';
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

    public function shouldActionDisplayOnDataType() {
        return $this->dataType->slug == 'hh-resumes';
    }

    public function shouldActionDisplayOnRow($row)
    {
        return ($row->is_doc === 1);
    }


    public function getDefaultRoute()
    {
        $resume = $this->data;
        return route('hhdoc', ['resume_id' => $resume->resume_id, 'id' => $resume->id, 'pdf_url' => $resume->pdf_url]);
    }

}
