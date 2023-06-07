<?php

namespace Application\Controllers;

use Application\Core\Controller;

class Help extends Controller
{
    public function index()
    {
        $this->view('help/help');
    }
}