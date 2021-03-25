<?php

namespace App;

use Routes\Router;

class App
{
    private Router $router;

    public function __construct()
    {
        $this->router = new Router();
        $this->router->create();
    }

    public function run()
    {
        //TODO
    }
}