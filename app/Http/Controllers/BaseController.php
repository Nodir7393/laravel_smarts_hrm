<?php

namespace App\Http\Controllers;

use App\Services\NutgramService;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    public $service;

    public function __construct(NutgramService $service)
    {
        $this->service = $service;
    }
}
