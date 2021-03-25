<?php

namespace Routes\Adapters;

use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use League\Route\Router;
use League\Route\Strategy\JsonStrategy;

class LeagueRouteAdapter implements RouterInterface
{
    private array $routes;
    private Router $router;
    private ServerRequest $request;

    public function __construct(array $routes)
    {
        $this->routes = $routes;
        $this->init();
    }

    private function init()
    {
        $this->request = ServerRequestFactory::fromGlobals(
            $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
        );

        $responseFactory = new ResponseFactory();
        $strategy = new JsonStrategy($responseFactory);

        $this->router = new Router();
        $this->router->setStrategy($strategy);
    }

    public function createRoutes()
    {
        foreach ($this->routes as $route) {
            $this->router->map($route['method'], $route['path'], 'App\Controllers\\' . $route['resource'] . '::' . $route['action']);
        }

        $response = $this->router->dispatch($this->request);
        (new SapiEmitter)->emit($response);
    }
}