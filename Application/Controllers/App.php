<?php

namespace Application\Controllers;

use \Error;

class App
{
    protected $controller = 'Index';
    protected $method = 'index';
    protected $params = [];

    public function __construct()
    {
        $url = $this->parseUrl();

        if ($url && $url[0] == 'app') {
            $url[0] = 'index';
        }

        if (isset($url[0])) {
            if (file_exists(__DIR__ . '/' . $url[0] . '.php')) {
                $this->controller = $url[0];
                unset($url[0]);
            }
        }

        $this->controller = 'Application\Controllers\\' . $this->controller;
        $this->controller = new $this->controller;

        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        $this->params = $url ? array_values($url) : [];

        try {
            call_user_func_array([$this->controller, $this->method], $this->params);
        } catch (Error $e) {
            echo '<i>Something unexpected happened.</i>' . '<br><b>' . $e . '</b>';
        }
    }

    protected function parseUrl()
    {
        if (isset($_GET['url'])) {
            $url = trim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            return explode('/', $url);
        }
    }
}
