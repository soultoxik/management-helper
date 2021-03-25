<?php
namespace Routes;

use Routes\Adapters\LeagueRouteAdapter;
use Symfony\Component\Yaml\Yaml;

class Router {

    private array $routes;

    public function __construct()
    {
        $this->routes = Yaml::parseFile(dirname(__DIR__, 1) . $_ENV['ROUTING_FILE_PATH']);
    }

    public function create()
    {
        $router = new LeagueRouteAdapter($this->routes);
        $router->createRoutes();
    }
}