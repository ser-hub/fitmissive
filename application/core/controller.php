<?php

namespace Application\Core;

class Controller
{
    protected function model($model)
    {
        require_once 'Application/Models/' . $model . '.php';
        return new $model;
    }

    protected function view($view, $data = [])
    {
        require_once 'Application/Views/' . $view . '.php';
    }
}
