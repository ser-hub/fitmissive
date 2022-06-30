<?php

class Home extends Controller
{
    public function index() 
    {
        //$user = $this->model('user');
        $this->view('home/index');
    }

    public function loginAction()
    {
        $this->view('test');

        echo $_POST['username'];
    }

    public function registerAction()
    {
        $this->view('test');

        echo $_POST['username'];
    }
}