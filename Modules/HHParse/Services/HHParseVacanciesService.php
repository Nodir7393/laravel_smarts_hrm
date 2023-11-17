<?php

namespace Modules\HHParse\Services;

use DiDom\Document;
use Modules\HHParse\Models\HhVacancie;

class HHParseVacanciesService
{
    public HHHelper $helper;

    public function __construct()
    {
        $this->helper = new HHHelper();
    }
}
