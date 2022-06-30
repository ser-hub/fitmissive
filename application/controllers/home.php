<?php

class Home extends Controller
{

    public function __construct()
    {
        require_once 'application/services/userService.php';   
    }

    public function index() 
    {
        //$user = $this->model('user');
        $this->view('home/index');
    }

    public function loginAction()
    {
        $this->view('test');
    }

    public function registerAction()
    {
        
    }
}