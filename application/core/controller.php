<?php 

class Controller 
{
    protected function model($model)
    {
        require_once 'application/models/' . $model . '.php';
        return new $model;
    }

    protected function view($view, $data = [])
    {
        require_once 'application/views/' . $view . '.php';
    }
}